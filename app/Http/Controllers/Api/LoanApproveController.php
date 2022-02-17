<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Services\LoanService;
use App\Http\Resources\Loan\LoanResource;
use Carbon\Carbon;
use JWTAuth;

class LoanApproveController extends Controller
{
    /**
     * Loan Service
     *
     * @var object
     */
    protected $loanService;

    /**
     * Initialize Loan Approve Controller
     *
     * @param LoanService $loanService
     */
    public function __construct(LoanService $loanService) {
        $this->loanService = $loanService;
    }

    /**
     * @OA\Get(
     * path="/api/approve-loan/{id}",
     * operationId="approveLoan",
     * tags={"Loan"},
     * summary="Approve Loan Request",
     * description="This api will use for approve loan request.",
     * security={ {"bearerAuth": {} }},
     * @OA\Parameter(
     *     name="id",
     *     description="Loan Id",
     *     required=true,
     *     in="path",
     *     name="id",
     *     @OA\Schema(
     *         type="integer"
     *     )
     * ),
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
    public function approveLoan($id) {
        $user = JWTAuth::user();
        if(!$user->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => __('loan.route_permission_denied'),
            ]);
        }
        $loan = $this->loanService->findById($id);
        if ($loan) {

            if ($loan->is_approved) {
                return response()->json([
                    'status' => false,
                    'message' => __('loan.loan_already_approved'),
                ]); 
            }

            // Approve Loan
            $loan->is_approved = true;
            $loan->approved_by = $user->id;
            $loan->save();

            // Store loan schedule
            $startDate =  Carbon::now();
            $endDate = Carbon::now();
            if ($loan->loan_term > 1) {
                $endDate->addYears($loan->loan_term);
            } else {
                $endDate->addYear($loan->loan_term);
            }

            $diffInWeeks = $startDate->diffInWeeks($endDate);
            for ($i=0; $i<$diffInWeeks; $i++) { 
                $weekDate = $startDate->addWeek(1);
                LoanRepayment::create([
                    'loan_id' => $loan->id,
                    'amount_due_date' => $weekDate->format('Y-m-d'),
                ]);
            }
            
            return response()->json([
                'status' => true,
                'message' => __('loan.loan_approved'),
                'data' => $loan ? new LoanResource($loan) : NULL
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('loan.not_found')
            ]);
        }
    }
}
