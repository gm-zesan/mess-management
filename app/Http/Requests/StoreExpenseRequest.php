<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MonthNotClosed;

class StoreExpenseRequest extends FormRequest
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
        // Get month_id from hidden field or request
        $monthId = $this->input('month_id') ?? request()->route('month')?->id;
        
        return [
            'user_id' => ['required', 'exists:users,id'],
            'category' => ['required', 'string', 'in:meal,utility'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
            // Check month closure through service in controller
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Who is spending is required',
            'user_id.exists' => 'Selected member does not exist',
            'month_id.required' => 'Month is required',
            'month_id.exists' => 'Selected month does not exist',
            'category.required' => 'Category is required',
            'category.max' => 'Category must not exceed 255 characters',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a valid number',
            'amount.min' => 'Amount must be 0 or greater',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a valid date',
        ];
    }
}
