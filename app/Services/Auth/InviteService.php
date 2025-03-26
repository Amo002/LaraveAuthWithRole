<?php

namespace App\Services\Auth;

use App\Mail\InviteMail;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class InviteService
{
    public function sendInvite($email)
    {
        if (User::whereEmail($email)->exists()) {
            return ['status' => false, 'message' => 'User already exists'];
        }

        $invite = Invite::where('email', $email)->first();

        if ($invite) {
            return ['status' => false, 'message' => "This email: $email already Invited."];
        }

        $email = strtolower(trim($email));
        $expiresAt = now()->addWeek()->setTimezone('Asia/Amman');
        // $expiresAt = now()->setTimezone('Asia/Amman');


        $invite = Invite::create([
            'email' => $email,
            'expires_at' => $expiresAt,
        ]);



        $this->sendInviteEmail($email, $invite->id);

        return ['status' => true, 'message' => 'Invite sent successfully'];
    }




    public function getAllInvites($search = null)
    {
        $query = Invite::query();

        if ($search) {
            $query->where('email', 'like', "%{$search}%");
        }

        $invites = $query->get()->map(function ($invite) {
            $expiresAt = \Carbon\Carbon::parse($invite->expires_at);

            return [
                'id' => $invite->id,
                'email' => $invite->email,
                'expires_at' => $expiresAt->format('d M Y, H:i A'),
                'status' => $expiresAt->isFuture() ? 'Valid' : 'Expired'
            ];
        });

        return $invites;
    }



    public function deleteInvite($id)
    {
        $invite = Invite::find($id);

        

        if (!$invite) {
            return ['status' => false, 'message' => 'Invite not found'];
        }

        $invite->delete();

        return ['status' => true, 'message' => 'Invite deleted successfully'];
    }

    public function resendInvite($id)
    {
        $invite = Invite::find($id);

        if (!$invite) {
            return ['status' => false, 'message' => 'No invite exists for this user.'];
        }

        if ($invite->expires_at->isFuture()) {

            $this->sendInviteEmail($invite->email, $invite->id);

            return ['status' => true, 'message' => 'resent successfully.'];
        }


        $invite->update([
            'expires_at' => Carbon::now()->addWeek()->setTimezone('Asia/Amman'),
        ]);



        $this->sendInviteEmail($invite->email, $invite->id);

        return ['status' => true, 'message' => 'Invite has been renewed and resent successfully.'];
    }






    private function sendInviteEmail($email, $inviteId)
    {
        $payload = encrypt([
            'email' => $email,
            'id' => $inviteId,
        ]);

        $url = URL::signedRoute('register.invite', [
            'payload' => $payload
        ]);

        Mail::to($email)->queue(new InviteMail($url));
    }
}
