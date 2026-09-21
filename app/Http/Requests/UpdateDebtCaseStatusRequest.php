<?php

namespace App\Http\Requests;

use App\Models\DebtCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDebtCaseStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    DebtCase::STATUS_NEW,
                    DebtCase::STATUS_IN_PROGRESS,
                    DebtCase::STATUS_CLOSED,
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The status is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
