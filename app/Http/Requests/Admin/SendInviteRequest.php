<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
