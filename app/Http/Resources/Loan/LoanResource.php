<?php

namespace App\Http\Resources\Loan;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'amount_required'   => $this->amount_required,
            'loan_term'         => $this->loan_term,
            'emi_amount'        => $this->emi_amount,
            'is_approved'       => $this->is_approved ?  true : false,
        ];
    }
}
