<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authUser = auth()->user();

        // Only super admin (merchant_id = 1) can send invites
        return $authUser && $authUser->hasRole('admin') && $authUser->merchant_id === 1;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
        ];
    }
}
