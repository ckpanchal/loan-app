<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $table = 'loan_repayments';

    protected $guarded = ['id'];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }
}
