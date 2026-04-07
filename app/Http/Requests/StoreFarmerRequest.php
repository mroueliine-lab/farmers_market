<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'identifier'   => 'required|string|unique:farmers,identifier',
        'firstname'    => 'required|string',
        'lastname'     => 'required|string',
        'phone_number' => 'required|string|unique:farmers,phone_number',
        'credit_limit' => 'required|numeric|min:0',
        ];
    }
}
