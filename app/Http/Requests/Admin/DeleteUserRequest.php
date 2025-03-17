<?php

namespace App\Http\Requests\Admin;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:users,id'
        ];
    }
}
