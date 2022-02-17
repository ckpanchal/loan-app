<?php

namespace App\Repositories;

use App\Models\Loan;

class LoanRepository
{
    public function findById($id) {
        return Loan::find($id);
    }

    public function create($user, $data) {
        
    }
}