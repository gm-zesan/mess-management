<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\MonthNotClosed;

class StoreMealRequest extends FormRequest
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
            'member_id' => ['required', 'exists:members,id'],
            'month_id' => ['required', 'exists:months,id', new MonthNotClosed()],
            'date' => [
                'required',
                'date',
                Rule::unique('meals')
                    ->where('member_id', $this->member_id)
                    ->where('month_id', $this->month_id)
                    ->ignore($this->meal ?? null),
            ],
            'meal_count' => ['required', 'integer', 'min:0', 'max:3'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'member_id.required' => 'Please select a member.',
            'member_id.exists' => 'The selected member does not exist.',
            'month_id.required' => 'Please select a month.',
            'month_id.exists' => 'The selected month does not exist.',
            'date.required' => 'Please enter a date.',
            'date.date' => 'Please enter a valid date.',
            'date.unique' => 'This member already has a meal entry for this date.',
            'meal_count.required' => 'Please enter the meal count.',
            'meal_count.integer' => 'Meal count must be a whole number.',
            'meal_count.min' => 'Meal count must be at least 0.',
            'meal_count.max' => 'Meal count cannot exceed 3.',
        ];
    }
}
