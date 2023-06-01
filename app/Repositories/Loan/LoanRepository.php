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
}
