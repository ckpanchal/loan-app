<?php

namespace App\Services;

use App\Repositories\LoanRepository;

class LoanService
{
    protected $loanRepo;

    public function __construct(LoanRepository $loanRepo) {
        $this->loanRepo = $loanRepo;
    }

    public function findById($id) {
        $loan = $this->loanRepo->findById($id);
        if ($loan) {
            return $loan;
        }
        return false;
    }
}