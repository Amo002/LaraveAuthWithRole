<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authUser = auth()->user();

        // Only allow global super admin (merchant_id = 1)
        return $authUser->hasRole('admin') && $authUser->merchant_id === 1;
    }

    public function rules(): array
    {
        return [
<<<<<<< HEAD
            'role' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($this->route('id'));
                    if (!$user) {
                        return $fail('User not found.');
                    }

                    // Set Spatie's team context
                    app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($user->merchant_id);

                    // Validate role exists for that team
                    if (!Role::where('name', $value)->where('merchant_id', $user->merchant_id)->exists()) {
                        $fail("Role '{$value}' is not valid for this merchant.");
                    }
                }
            ],
=======
            'role' => 'required|string|in:admin,merchant,user',
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
        ];
    }
}
