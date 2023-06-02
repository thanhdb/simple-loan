<?php

namespace App\Repositories\Loan;

use App\Models\Loan;

interface LoanRepositoryInterface
{
    /**
     * Get all loans for user logged in
     * @return mixed
     */
    public function getLoans();

    /**
     * Create new loan
     * @param $requestData
     * @return mixed
     */
    public function create($requestData);

    /**
     * Admin Approve a loan
     * @param Loan $loan
     * @return mixed
     */
    public function approve(Loan $loan);

    /**
     * Get loan by uuid
     * @param $uuid
     * @return mixed
     */
    public function getLoanByUuid($uuid);

    /**
     * Check loan status
     * @param Loan $loan
     * @param $statusSlug
     * @return mixed
     */
    public function checkStatus(Loan $loan, $statusSlug);

    /**
     * Get loan string status
     * @param Loan $loan
     * @return mixed
     */
    public function getStatus(Loan $loan);
}
