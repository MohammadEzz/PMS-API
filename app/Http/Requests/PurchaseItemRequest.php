<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PurchaseItemRequest extends FormRequest
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
            'purchasebill_id' => 'required|integer|min:1',
            'drug_id' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'bonus' => 'nullable|integer|min:0',
            'sellprice' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'expiredate' => 'required|date',
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
