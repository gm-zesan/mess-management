<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepositRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            // Check month closure through service in controller
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Member is required',
            'user_id.exists' => 'Selected member does not exist',
            'month_id.required' => 'Month is required',
            'month_id.exists' => 'Selected month does not exist',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a valid number',
            'amount.min' => 'Amount must be 0 or greater',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a valid date',
        ];
    }
}
