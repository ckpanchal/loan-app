<?php

namespace App\Repositories;

use App\Models\Loan;

class LoanRepository
{
    /**
     * Handle logic to get data by id 
     *
     * @param integer $id
     * @return void
     */
    public function findById($id) {
        return Loan::find($id);
    }

    /**
     * Handle logic to store loan application data
     *
     * @param integer $user
     * @param array $data
     * @return void
     */
    public function create($user, $data) {
        return Loan::create($data);
    }
}