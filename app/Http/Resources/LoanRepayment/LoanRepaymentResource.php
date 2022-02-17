<?php

namespace App\Http\Resources\LoanRepayment;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Loan\LoanResource;

class LoanRepaymentResource extends JsonResource
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
            'loan'              => $this->loan ? new LoanResource($this->loan) : NULL,
            'amount_due_date'   => $this->amount_due_date,
            'paid_on'           => $this->paid_on,
        ];
    }
}
