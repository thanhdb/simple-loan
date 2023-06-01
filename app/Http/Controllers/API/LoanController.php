<?php

namespace App\Http\Controllers\API;

use App\Http\Traits\ApiResponseTrait;
use App\Repositories\Loan\LoanRepositoryInterface;
use App\Http\Requests\Loan\CreateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

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
            'data' => [
                'loan' => $loanData['loan'],
                'scheduled_payments' => $loanData['scheduled_payments'],
            ],
            'message' => 'Loan created successfully'
        ];

        return $this->successResponse($responseData['data'], $responseData['message']);
    }
}
