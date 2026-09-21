<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDebtCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => is_string($this->description)
                ? trim($this->description)
                : $this->description,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'description' => [
                'required',
                'string',
                'max:500',
            ],

            'debt_amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'id' => ['prohibited'],
            'status' => ['prohibited'],
            'opened_at' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'The client ID is required.',
            'client_id.exists' => 'The selected client ID is invalid.',
            'description.required' => 'The description is required.',
            'debt_amount.required' => 'The debt amount is required.',
            'debt_amount.numeric' => 'The debt amount must be a number.',
            'debt_amount.gt' => 'The debt amount must be greater than 0.',
            'debt_amount.decimal' => 'The debt amount may have at most two decimal places.',
            'id.prohibited' => 'The ID is generated automatically and cannot be provided.',
            'status.prohibited' => 'The status is generated automatically and cannot be provided.',
            'opened_at.prohibited' => 'The opening date is generated automatically and cannot be provided.',
        ];
    }
}
