<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authUser = auth()->user();

        // Only super admin (merchant_id = 1) can delete
        return $authUser && $authUser->hasRole('admin') && $authUser->merchant_id === 1;
    }

    public function rules()
    {
        return [
            'id' => [
                'required',
                'exists:users,id',
                // Prevent deleting the super admin
                function ($attribute, $value, $fail) {
                    if ($value == 1) {
                        $fail('Cannot delete the super admin.');
                    }
                },
            ],
        ];
    }
}
