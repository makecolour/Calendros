<?php

namespace App\Jobs;

use App\Mail\EventInvitationMail;
use App\Models\Invite;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEventInvitation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Invite $invite)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->invite->invitee_email)
            ->send(new EventInvitationMail($this->invite));
    }
}
