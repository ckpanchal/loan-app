<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Loan;
use App\Models\LoanRePayment;
use Tests\TestCase;
use JWTAuth;

class LoanApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_loan_applied_successfuly()
    {
        $this->withoutExceptionHandling();
        $user = User::where(['email' => 'user@example.com'])->first();
        $token = JWTAuth::fromUser($user);
        $headers['Authorization'] = 'Bearer ' . $token;
        $formData = [
            'amount_required' => 50000,
            'loan_term' => 5,
        ];
        $response = $this->json('POST', route('loan.applyForLoan'), $formData, $headers);
        $decodeResponse = $response->decodeResponseJson();
        $response->assertStatus(200);
        $this->assertEquals(true, $decodeResponse['status']);
    }

    public function test_emi_paid_successfuly()
    {
        $this->withoutExceptionHandling();
        
        // Apply Loan
        $user = User::where(['email' => 'user@example.com'])->first(); // User
        $userToken = JWTAuth::fromUser($user);
        $headers['Authorization'] = 'Bearer ' . $userToken;
        $formData = [
            'amount_required' => 50000,
            'loan_term' => 5,
        ];
        $response = $this->json('POST', route('loan.applyForLoan'), $formData, $headers);
        $decodeResponse = $response->decodeResponseJson();
        $response->assertStatus(200);
        $this->assertEquals(true, $decodeResponse['status']);

        // Approve Loan
        $adminUser = User::where(['email' => 'admin@example.com'])->first(); // Admin
        $adminToken = JWTAuth::fromUser($adminUser);
        $loan = Loan::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->json('GET', route('loan.approveLoan', ['id' => $loan->id]));
        $response->assertStatus(200);
        $decodeResponse = $response->decodeResponseJson();
        $this->assertEquals(true, $decodeResponse['status']);

        // Emi Payment
        $loanPayment = LoanRePayment::first();
        $loan = Loan::first();
        $emiAmount = $loan->emi_amount;
        $emiFormData = [
            'emi_id' => $loanPayment->id,
            'amount_paid' => $emiAmount
        ];
        $response = $this->json('POST', route('loan.emiPayment'), $emiFormData, $headers);
        $response->assertStatus(200);
        $decodeResponse = $response->decodeResponseJson();
        $this->assertEquals(true, $decodeResponse['status']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
