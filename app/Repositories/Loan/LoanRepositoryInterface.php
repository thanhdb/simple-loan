<?php

namespace App\Repositories\Loan;

interface LoanRepositoryInterface
{
    /*
     * Create new loan
     */
    public function create($requestData);
}
