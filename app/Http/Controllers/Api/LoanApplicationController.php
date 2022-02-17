<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\ApplyForLoanRequest;
use App\Http\Requests\Loan\EmiPaymentRequest;
use App\Http\Resources\Loan\LoanResource;
use App\Http\Resources\LoanRepayment\LoanRepaymentResource;
use Carbon\Carbon;
use JWTAuth;

class LoanApplicationController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/apply-for-loan",
     * operationId="applyForLoan",
     * tags={"Loan"},
     * summary="Apply For Loan",
     * description="This api will use for submit loan application request.",
     * security={ {"bearerAuth": {} }},
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"amount_required","loan_term"},
     *              @OA\Property(property="amount_required", type="string", example="50000"),
     *              @OA\Property(property="loan_term", type="string", example="2", description="In Year"), 
     *          ),
     *       ),
     *   ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Validator Error"
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Success"
     *     ),
     * )
     */
    public function applyForLoan(ApplyForLoanRequest $request) {
        $requestData = $request->input();
        $user = JWTAuth::user();
        if($user->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => __('loan.route_permission_denied'),
            ], 403);
        }
        $loan = Loan::create([
            'amount_required'   => $request->amount_required,
            'loan_term'         => $request->loan_term,
            'user_id'           => $user->id,
        ]);
        if ($loan) {

            // Calculate weekly EMI
            $term = $loan->loan_term*52;
            $amount = $loan->amount_required;
            $rate = config('loan.INTEREST_RATE')/(52*100);
            $emi = $amount * $rate * (pow(1 + $rate, $term) / (pow(1 + $rate, $term) - 1));
            $loan->emi_amount = number_format($emi, 2);
            $loan->save();

            return response()->json([
                'status' => true,
                'message' => __('loan.loan_applied'),
                'data' => $loan ? new LoanResource($loan) : NULL
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('loan.unexpected_error')
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/emi-payment",
     * operationId="emiPayment",
     * tags={"Loan"},
     * summary="Pay Loan EMI",
     * description="This api will use for submit loan emi payment.",
     * security={ {"bearerAuth": {} }},
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"amount_paid","emi_id"},
     *              @OA\Property(property="amount_paid", type="string", example="5000"),
     *              @OA\Property(property="emi_id", type="integer", example="1", description="Primary ID from loan repayments table"), 
     *          ),
     *       ),
     *   ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Validator Error"
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Success"
     *     ),
     * )
     */
    public function emiPayment(EmiPaymentRequest $request) {
        $user = JWTAuth::user();
        $id = $request->emi_id;
        $amount = $request->amount_paid;
        $loanRepayment = LoanRepayment::find($id);
        if ($loanRepayment) {
            if ($loanRepayment->paid_on == null) {
                $loanRepayment->amount_paid = $amount;
                $loanRepayment->paid_on = Carbon::now();
                $loanRepayment->paid_by = $user->id;
                $loanRepayment->save();
                return response()->json([
                    'status' => true,
                    'message' => __('loan.emi_paid'),
                    'data' => $loanRepayment ? new LoanRepaymentResource($loanRepayment) : NULL
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('loan.emi_already_paid'),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => __('loan.not_found'),
            ], 400);
        }
    }
}
