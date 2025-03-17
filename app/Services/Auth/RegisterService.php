<?php

namespace App\Services\Auth;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterService
{
    public function decryptInvite($encryptedPayload)
    {
        try {
            $data = decrypt($encryptedPayload);

            if (!isset($data['email']) || !isset($data['id'])) {
                return [
                    'status' => false,
                    'message' => 'Invalid invite payload.'
                ];
            }

            return [
                'status' => true,
                'email' => $data['email'],
                'id' => $data['id']
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Invalid or tampered invite link.'
            ];
        }
    }


    public function validateInvite($email, $id)
    {
        $email = strtolower(trim($email));

        $invite = Invite::whereRaw('LOWER(email) = ?', [$email])
            ->where('id', $id)
            ->first();

        if (!$invite) {
            return [
                'status' => false,
                'message' => 'Invite not found.'
            ];
        }

        if ($invite->expires_at->isPast()) {
            return [
                'status' => false,
                'message' => 'Invite has expired.'
            ];
        }

        return [
            'status' => true,
            'invite' => $invite
        ];
    }



    public function completeRegistration($data)
    {
        $invite = Invite::where('email', $data['email'])
            ->where('id', $data['id'])
            ->first();

        if (!$invite) {
            return [
                'status' => false,
                'message' => 'Invite not found.'
            ];
        }

        if ($invite->expires_at->isPast()) {
            return [
                'status' => false,
                'message' => 'Invalid or expired invite.'
            ];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('user');

        $invite->delete();

        Auth::login($user);

        return [
            'status' => true,
            'message' => 'Account created successfully!',
            'user' => $user
        ];
    }
}
