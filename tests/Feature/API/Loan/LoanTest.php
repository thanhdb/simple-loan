<?php

namespace Tests\Feature\API\Loan;

use App\Models\Status;
use Laravel\Sanctum\Sanctum;
use Tests\BaseApiTestCase;

class LoanTest extends BaseApiTestCase
{

    /**
     * Data provider for create loan
     * @return array[]
     */
    public function dataForCreateLoan()
    {
        $successStatus = 200;
        $failedStatus = 400;
        return [
            [
                ['amount' => 1000, 'term' => 12], $successStatus
            ],
            [
                ['amount' => -1000, 'term' => 12], $failedStatus
            ],
            [
                ['amount' => 1000, 'term' => -36], $failedStatus
            ]
        ];
    }

    /**
     * Test user can create a loan
     * @test
     * @dataProvider dataForCreateLoan
     * @return void
     */
    public function test_user_can_create_a_loan($data, $expectedStatus)
    {
        $this->fakeLoginUser();

        $response = $this->postJson(route('create-loan'), $data);
        $this->assertEquals($response->status(), $expectedStatus);
    }

    /**
     * Test user view loan
     * @test
     */
    public function test_user_can_view_loan()
    {
        $this->fakeLoginUser();
        $dataNewLoan = ['amount' => 9999, 'term' => 12];
        $response = $this->postJson(route('create-loan'), $dataNewLoan);
        $response->assertStatus(200);

        $responseArr = $response->json();
        $loanUuid = $responseArr['data']['loan']['uuid'];
        $responseViewLoan = $this->getJson(route('view-loan', $loanUuid));
        $responseViewLoan->assertStatus(200);
    }

    /**
     * Test user cannot view loan not owned by self
     * @test
     */
    public function test_user_cannot_view_loan_not_owned_by_self()
    {
        //user 1 create loan
        $user = $this->fakeLoginUser();
        $dataNewLoan = ['amount' => 1000, 'term' => 12];
        $loanUuid = $this->userCreateNewLoan($user, $dataNewLoan);

        //login as user 2 to view loan
        $this->fakeLoginUser();
        $responseViewLoan = $this->getJson(route('view-loan', $loanUuid));
        $responseViewLoan->assertStatus(403);
    }

    /**
     * Test admin can approve loan
     * @test
     */
    public function test_admin_can_approve_loan()
    {
        //user customer create loan
        $user = $this->fakeLoginUser();
        $dataNewLoan = ['amount' => 400, 'term' => 12];
        $loanUuid = $this->userCreateNewLoan($user, $dataNewLoan);

        //login as admin to approve loan
        $this->fakeLoginUser('admin');
        $responseApproveLoan = $this->postJson(route('approve-loan', ['loan_uuid' => $loanUuid]));
        $responseApproveLoan->assertStatus(200);
    }

    /**
     * Test user cannot approve loan
     * @test
     */
    public function test_user_cannot_approve_loan()
    {
        //user customer create loan
        $user = $this->fakeLoginUser();
        $dataNewLoan = ['amount' => 100, 'term' => 12];
        $loanUuid = $this->userCreateNewLoan($user, $dataNewLoan);

        $responseApproveLoan = $this->postJson(route('approve-loan', ['loan_uuid' => $loanUuid]));
        $responseApproveLoan->assertStatus(403);

        //user customer 2 cannot approve loan also
        $this->fakeLoginUser();
        $responseApproveLoan = $this->postJson(route('approve-loan', ['loan_uuid' => $loanUuid]));
        $responseApproveLoan->assertStatus(403);
    }

    /**
     * Test user payment loan
     * @test
     */
    public function test_user_can_payment_loan()
    {
        //user customer create loan
        $user = $this->fakeLoginUser();
        $dataNewLoan = ['amount' => 1000, 'term' => 1];
        $loanUuid = $this->userCreateNewLoan($user, $dataNewLoan);

        //admin approve loan
        $this->fakeLoginUser('admin');
        $responseApproveLoan = $this->postJson(route('approve-loan', ['loan_uuid' => $loanUuid]));
        $responseApproveLoan->assertStatus(200);

        //user payment loan
        Sanctum::actingAs($user);
        $responseApproveLoan = $responseApproveLoan->json();
        $dataPayment = [
            'loan_uuid' => $loanUuid,
            'scheduled_payment_uuid' => $responseApproveLoan['data']['scheduled_payments'][0]['uuid'],
            'amount' => $responseApproveLoan['data']['scheduled_payments'][0]['amount'],
        ];
        $responsePaymentLoan = $this->postJson(route('repayment-loan'), $dataPayment);
        $responsePaymentLoan->assertStatus(200);

        //the loan has 1 term and has paid, so the loan status should be paid
        $responsePaymentLoan = $responsePaymentLoan->json();
        $this->assertEquals($responsePaymentLoan['data']['loan']['status_id'], Status::getIdBySlug('paid'));
    }

    /**
     * @param $user
     * @param $dataNewLoan
     * @return mixed
     */
    private function userCreateNewLoan($user, $dataNewLoan)
    {
        Sanctum::actingAs($user);
        $responseCreateLoan = $this->postJson(route('create-loan'), $dataNewLoan);
        $responseCreateLoan->assertStatus(200);
        $responseCreateLoanArr = $responseCreateLoan->json();
        $loanUuid = $responseCreateLoanArr['data']['loan']['uuid'];
        return $loanUuid;
    }

}
