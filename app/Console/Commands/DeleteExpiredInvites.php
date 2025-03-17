<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeleteExpiredInvites extends Command
{
    protected $signature = 'invites:delete-expired';
    protected $description = 'Delete all expired invites from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Count how many expired invites were deleted
        $count = Invite::where('expires_at', '<', Carbon::now())->delete();

        //Log the deletion
        Log::info("Deleted {$count} expired invites at " . now());

        //Show message in the console
        $this->info("Deleted {$count} expired invites.");
    }
}
