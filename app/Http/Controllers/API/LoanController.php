<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Loan\ApproveRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Loan;
use App\Models\User;
use App\Repositories\Loan\LoanRepositoryInterface;
use App\Http\Requests\Loan\CreateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    use ApiResponseTrait;
    /**
     * @var LoanRepositoryInterface $loanRepository
     */
    private $loanRepository;

    /**
     * @param LoanRepositoryInterface $loanRepository
     */
    public function __construct(LoanRepositoryInterface $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    public function index()
    {
        $loans = $this->loanRepository->getLoans();
        return $this->successResponse($loans, 'Loans retrieved successfully');
    }

    /**
     * Create a new Loan
     * @param  CreateRequest $request
     * @return JsonResponse
     */
    public function create(CreateRequest $request)
    {
        $requestData = $request->validated();
        $loanData = $this->loanRepository->create($requestData);
        $responseData = [
            'loan' => $loanData['loan'],
            'scheduled_payments' => $loanData['scheduled_payments'],
        ];

        return $this->successResponse($responseData, 'Loan created successfully');
    }

    /**
     * View a loan
     * @param $uuid
     * @return JsonResponse
     */
    public function view($uuid)
    {
        $loan = $this->loanRepository->getLoanByUuid($uuid);

        if (!$loan) {
            return $this->errorResponse('Loan not found', Response::HTTP_NOT_FOUND);
        }

        //only admin and loan owner can view the loan
        $this->authorize('view', $loan);

        $responseData = [
            'loan' => $loan,
            'scheduled_payments' => $loan->scheduledPayments,
        ];

        return $this->successResponse($responseData, 'Loan retrieved successfully');
    }

    /**
     * Admin can approve a loan
     * @param ApproveRequest $request
     * @return JsonResponse
     */
    public function approve(ApproveRequest $request)
    {
        $requestData = $request->validated();

        //check loan is exist and pending
        $loan = $this->loanRepository->getLoanByUuid($requestData['loan_uuid']);
        if (!$loan) {
            return $this->errorResponse('Loan not found', Response::HTTP_NOT_FOUND);
        }

        if ($this->loanRepository->checkStatus($loan, 'pending') != true) {
            return $this->errorResponse('Loan is not pending', Response::HTTP_BAD_REQUEST);
        }

        //if loan is pending then approve it
        $loanData = $this->loanRepository->approve($loan);

        $responseData = [
            'loan' => $loanData['loan'],
            'scheduled_payments' => $loanData['scheduled_payments'],
        ];

        return $this->successResponse($responseData, 'Loan approved successfully');
    }
}
