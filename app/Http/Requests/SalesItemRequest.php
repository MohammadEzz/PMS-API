<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesItemRequest extends FormRequest
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
        if($this->getMethod() === 'POST') {
            return [
                'inventory_id' => 'required|integer|min:1',
                'bill_id' => 'required|integer|min:1',
            ];
        }
        elseif($this->getMethod() === 'PUT') {
            return [
                'quantity' => 'required|numeric|min:0.1|max:99.99',
                'discount' => 'nullable|numeric|min:0',
            ];
        }
       
    }
}
