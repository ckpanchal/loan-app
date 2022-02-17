<?php

namespace App\Http\Requests\Loan;

use App\Http\Requests\FormRequest;

class EmiPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'emi_id' => 'required',
            'amount_paid' => 'required',
        ];
    }
}
