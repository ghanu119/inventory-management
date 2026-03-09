<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreFinanceTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense,transfer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'account_from' => ['nullable', 'in:cash,bank'],
            'account_to' => ['nullable', 'in:cash,bank'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = $this->input('type');
            $from = $this->input('account_from');
            $to = $this->input('account_to');

            if ($type === 'income') {
                if (empty($to)) {
                    $validator->errors()->add('account_to', __('Account to is required for income.'));
                }
                if (! empty($from)) {
                    $validator->errors()->add('account_from', __('Account from must be empty for income.'));
                }
            }

            if ($type === 'expense') {
                if (empty($from)) {
                    $validator->errors()->add('account_from', __('Account from is required for expense.'));
                }
                if (! empty($to)) {
                    $validator->errors()->add('account_to', __('Account to must be empty for expense.'));
                }
            }

            if ($type === 'transfer') {
                if (empty($from)) {
                    $validator->errors()->add('account_from', __('Account from is required for transfer.'));
                }
                if (empty($to)) {
                    $validator->errors()->add('account_to', __('Account to is required for transfer.'));
                }
                if (! empty($from) && ! empty($to) && $from === $to) {
                    $validator->errors()->add('account_to', __('Transfer must be between different accounts.'));
                }
            }
        });
    }
}

