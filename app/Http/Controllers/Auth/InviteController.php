<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendInviteRequest;
use App\Services\Auth\InviteService;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    protected $inviteService;

    public function __construct(InviteService $inviteService)
    {
        $this->inviteService = $inviteService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $invites = $this->inviteService->getAllInvites($search);
    
        return view('admin.invites', [
            'invites' => $invites,
            'search' => $search
        ]);
    }
    

    public function invite(SendInviteRequest $request)
    {
        $result = $this->inviteService->sendInvite($request->email);

        if ($result['status']) {
            return redirect()->route('invites.index')->with('success', $result['message']);
        }

        return redirect()->route('invites.index')->with('error', $result['message']);
    }


    public function deleteInvite($id)
    {
        $result = $this->inviteService->deleteInvite($id);

        return redirect()->route('invites.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function resendInvite($id)
    {
        $result = $this->inviteService->resendInvite($id);

        return redirect()->route('invites.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}
