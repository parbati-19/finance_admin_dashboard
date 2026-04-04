<?php

namespace App\Http\Requests\Transactions;

use App\Models\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'type' => ['required', Rule::enum(TransactionType::class)],
            'category' => 'required|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:5000',
        ];
    }
}
