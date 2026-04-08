<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, [UserRole::Admin, UserRole::Supervisor, UserRole::Operator]);
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
