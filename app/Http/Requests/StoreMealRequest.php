<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'date' => ['required', 'date'],
            'meals' => ['required', 'array'],
            'meals.*.breakfast_count' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'meals.*.lunch_count' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'meals.*.dinner_count' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Please select a date.',
            'date.date' => 'Please enter a valid date.',
            'meals.required' => 'Please select at least one member with meals.',
            'meals.array' => 'Invalid meal data format.',
            'meals.*.breakfast_count.numeric' => 'Breakfast count must be a number.',
            'meals.*.breakfast_count.min' => 'Breakfast count must be 0 or greater.',
            'meals.*.breakfast_count.max' => 'Breakfast count must not exceed 10.',
            'meals.*.lunch_count.numeric' => 'Lunch count must be a number.',
            'meals.*.lunch_count.min' => 'Lunch count must be 0 or greater.',
            'meals.*.lunch_count.max' => 'Lunch count must not exceed 10.',
            'meals.*.dinner_count.numeric' => 'Dinner count must be a number.',
            'meals.*.dinner_count.min' => 'Dinner count must be 0 or greater.',
            'meals.*.dinner_count.max' => 'Dinner count must not exceed 10.',
        ];
    }
}
