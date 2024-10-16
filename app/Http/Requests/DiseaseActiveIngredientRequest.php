<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DiseaseActiveIngredientRequest extends FormRequest
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
        if($this->getMethod() === "POST") {
            return [
                "activeingredient_id" => 'required|numeric|min:1',
                'order' => 'required|numeric|min:0'
            ];
        }
        elseif($this->getMethod() === "PUT") {
            return [
                'order' => 'required|numeric|min:0'
            ];
        }
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
