<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Operator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'farmer_id'              => 'required|exists:farmers,id',
        'payment_method'         => 'required|in:cash,credit',
        'items'                  => 'required|array|min:1',
        'items.*.product_id'     => 'required|exists:products,id',
        'items.*.quantity'       => 'required|integer|min:1',
        ];
    }
}
