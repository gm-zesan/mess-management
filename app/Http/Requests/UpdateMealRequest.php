<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MonthNotClosed;

class UpdateMealRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'breakfast_count' => ['required', 'numeric', 'min:0'],
            'lunch_count' => ['required', 'numeric', 'min:0'],
            'dinner_count' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a member.',
            'user_id.exists' => 'The selected member does not exist.',
            'date.required' => 'Please enter a date.',
            'date.date' => 'Please enter a valid date.',
            'breakfast_count.required' => 'Please enter breakfast count.',
            'breakfast_count.numeric' => 'Breakfast count must be a number.',
            'breakfast_count.min' => 'Breakfast count must be 0 or greater.',
            'lunch_count.required' => 'Please enter lunch count.',
            'lunch_count.numeric' => 'Lunch count must be a number.',
            'lunch_count.min' => 'Lunch count must be 0 or greater.',
            'dinner_count.required' => 'Please enter dinner count.',
            'dinner_count.numeric' => 'Dinner count must be a number.',
            'dinner_count.min' => 'Dinner count must be 0 or greater.',
        ];
    }
}
