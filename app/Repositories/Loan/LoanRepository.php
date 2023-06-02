<?php

namespace App\Repositories\Loan;

use App\Models\Loan;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoanRepository implements LoanRepositoryInterface
{
    /**
     * Get all loans for user logged in
     * @return mixed
     */
    public function getLoans()
    {
        return Loan::with('user', 'scheduledPayments')->where('user_id', Auth::id())->get();
    }
    /**
     * Create new loan and scheduled payments for it
     * @param $requestData
     * @return array
     */
    public function create($requestData)
    {
        $loanAmount = $requestData['amount'];
        $loanTerm   = $requestData['term'];

        //create loan
        $loanCreated = Loan::create([
            'uuid'      => Str::orderedUuid(),
            'user_id'   => Auth::id(),
            'amount'    => $loanAmount,
            'term'      => $loanTerm,
            'status_id' => Status::getIdBySlug('pending'),
            'frequency' => Loan::FREQUENCY
        ]);

        //create scheduled payments
        $amountInEachTerm = ($loanAmount / $loanTerm);
        $schedulePaymentArr = [
            'date'      => now(),
            'amount'    => number_format((float)$amountInEachTerm, 2, '.', ''),
            'status_id' => Status::getIdBySlug('pending')
        ];

        $schedulePaymentCreateArr = [];
        for ($i = 0; $i < $loanTerm; $i++) {
            $schedulePaymentArr['uuid'] = Str::orderedUuid();
            $schedulePaymentArr['date'] = Carbon::parse($schedulePaymentArr['date'])
                                                    ->addDays(7)->format('Y-m-d');
            $schedulePaymentCreateArr[] = $schedulePaymentArr;
        }

        $scheduledPaymentCreated = $loanCreated->scheduledPayments()->createMany($schedulePaymentCreateArr);

        $loanData = [
            'loan' => $loanCreated,
            'scheduled_payments' => $scheduledPaymentCreated
        ];

        return $loanData;
    }

    /**
     * Get loan by uuid
     * @param $uuid
     * @return mixed
     */
    public function getLoanByUuid($uuid)
    {
        return Loan::where('uuid', $uuid)->first();
    }

    /**
     * Approve a loan
     * @param Loan $loan
     * @return array
     */
    public function approve(Loan $loan)
    {
        $loan->status_id = Status::getIdBySlug('approved');
        $loan->save();

        return [
            'loan' => $loan,
            'scheduled_payments' => $loan->scheduledPayments
        ];
    }

    /**
     * Check loan status by slug
     * @param Loan $loan
     * @param $statusSlug
     * @return bool
     */
    public function checkStatus(Loan $loan, $statusSlug)
    {
        switch ($statusSlug) {
            case 'pending':
                return $loan->status_id == Status::getIdBySlug('pending');
            case 'approved':
                return $loan->status_id == Status::getIdBySlug('approved');
            case 'paid':
                return $loan->status_id == Status::getIdBySlug('paid');
            default:
                return false;
        }
    }

    /**
     * Get loan string status
     * @param Loan $loan
     * @return null
     */
    public function getStatus(Loan $loan)
    {
        $status = Status::getSlugById($loan->status_id)->first();
        return $status ? $status->slug : null;
    }
}
