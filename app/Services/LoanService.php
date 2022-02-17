<?php

namespace App\Services;

use App\Repositories\LoanRepository;

class LoanService
{
    /**
     * Repository associate with this service
     *
     * @var object
     */
    protected $loanRepo;

    /**
     * Initialize loan service
     *
     * @param LoanRepository $loanRepo
     */
    public function __construct(LoanRepository $loanRepo) {
        $this->loanRepo = $loanRepo;
    }

    /**
     * Handle operation to get loan data by id
     *
     * @param integer $id
     * @return void
     */
    public function findById($id) {
        $loan = $this->loanRepo->findById($id);
        return $loan ? $loan : FALSE;
    }

    /**
     * Handle operation to store loan application data
     *
     * @param object $user
     * @param array $data
     * @return void
     */
    public function create($user, $data) {
        $data['user_id'] = $user->id;
        $loan = $this->loanRepo->create($user, $data);
        return $loan ? $loan : FALSE;
    }

    /**
     * Handle opration to calculate emi based on loan amount, loan term and interest rate
     *
     * @param float $amount
     * @param integer $term
     * @param float $interestRate
     * @return void
     */
    public function calculateEmi($amount, $term, $interestRate) {
        $term = $term*52;
        $rate = $interestRate/(52*100);
        $emi = $amount * $rate * (pow(1 + $rate, $term) / (pow(1 + $rate, $term) - 1));
        return $emi;
    }
}