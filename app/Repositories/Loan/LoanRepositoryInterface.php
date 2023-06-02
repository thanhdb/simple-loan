<?php

namespace App\Repositories\Loan;

use App\Models\Loan;

interface LoanRepositoryInterface
{
    /*
     * Create new loan
     */
    public function create($requestData);

    public function approve(Loan $loan);

    public function getLoanByUuid($uuid);

    public function checkStatus(Loan $loan, $statusSlug);

    public function getStatus(Loan $loan);
}
