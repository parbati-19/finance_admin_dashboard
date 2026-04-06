<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->route('user')->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'nullable|in:0,1',
            'role' => 'nullable|string|exists:roles,name',
        ];
    }
}