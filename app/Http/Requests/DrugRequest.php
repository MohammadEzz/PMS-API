<?php

namespace App\Http\Requests;

use App\Http\Helpers\Api\ApiMessagesTemplate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DrugRequest extends FormRequest
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
            'name' => 'required|min:4|max:255',
            'brandname' => 'nullable|min:4|max:255',
            'type' => 'required|numeric|min:1',
            'description' => 'nullable|min:4',
            'barcode' => 'nullable|numeric|min:0',
            'middleunitnum' => 'required|numeric|min:1|max:100',
            'smallunitnum' => 'nullable|numeric|min:1|max:100',
            'visible' => 'required|boolean',
            'created_by' => 'required|numeric|min:1'
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
