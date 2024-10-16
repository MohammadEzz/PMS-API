<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DrugInteractionRequest extends FormRequest
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
            'activeingredient1' => 'required|numeric|min:1',
            'activeingredient2' => 'required|numeric|min:1|different:activeingredient1',
            'level' => 'required|numeric|min:1',
            'description' => 'nullable|min:4',
        ];
    }
}
