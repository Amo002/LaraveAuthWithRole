<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'role' => 'required|string|in:admin,user',
        ];
    }

    public function messages()
    {
        return [
            'role.required' => 'Role is required.',
            'role.in' => 'Invalid role. Only "admin" or "user" are allowed.',
        ];
    }
}
