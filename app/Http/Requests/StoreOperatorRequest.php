<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreOperatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Supervisor;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                    'name'     => 'required|string',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        ];
    }
}
