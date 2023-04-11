<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormDataRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|min:5',
            'email' => 'required',
            'contact' => 'required',
            'description' => 'required',
        ];
    }

    public function messages() {
        return [
            'name.required' => 'Name is Must',
            'name.min' => 'Name field cannot be empty',
            'email' => '@ and .com is required',
            'description.required' => 'Description field cannot be empty',
        ];
    }
}
