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
     * Get all loans for admin
     * @return mixed
     */
    public function getAllLoans()
    {
        return Loan::with('user', 'scheduledPayments')->paginate();
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
     * Payment for loan
     * @param Loan $loan
     * @param $requestData
     * @return array
     */
    public function payment(Loan $loan, $requestData)
    {
        //check amount greater than or equal to scheduled payment amount
        $scheduledPayment = $loan->scheduledPayments()
            ->where('uuid', $requestData['scheduled_payment_uuid'])
            ->where('status_id', Status::getIdBySlug('pending'))
            ->first();
        if (!$scheduledPayment) {
            return [
                'error' => 'Scheduled payment not found or already paid.'
            ];
        }
        if ($requestData['amount'] < $scheduledPayment->amount) {
            return [
                'error' => 'Amount must be greater than or equal to scheduled payment amount'
            ];
        } else {
            $scheduledPayment->status_id = Status::getIdBySlug('paid');
            $scheduledPayment->amount_paid = $requestData['amount'];
            $scheduledPayment->save();

            //if all scheduled payments are paid, change loan status to paid
            $scheduledPayments = $loan->scheduledPayments()
                ->where('status_id', Status::getIdBySlug('pending'))->get();
            if ($scheduledPayments->count() == 0) {
                $loan->status_id = Status::getIdBySlug('paid');
                $loan->save();
            }

            return [
                'loan' => $loan,
                'scheduled_payment' => $scheduledPayment,
                'error' => null
            ];
        }
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
