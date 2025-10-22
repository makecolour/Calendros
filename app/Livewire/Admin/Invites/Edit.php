<?php

namespace App\Livewire\Admin\Invites;

use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use Livewire\Component;

class Edit extends Component
{
    public $inviteId;
    public $eventId;
    public $userId;
    public $inviteeEmail;
    public $status;

    protected function rules()
    {
        return [
            'eventId' => 'required|exists:events,id',
            'userId' => 'required|exists:users,id',
            'inviteeEmail' => 'required|email',
            'status' => 'required|in:pending,accepted,rejected',
        ];
    }

    public function mount($inviteId)
    {
        $invite = Invite::with('event')->findOrFail($inviteId);
        $this->inviteId = $invite->id;
        $this->eventId = $invite->event_id;
        $this->userId = $invite->user_id;
        $this->inviteeEmail = $invite->invitee_email;
        $this->status = $invite->status;
    }

    public function save()
    {
        $this->validate();

        $invite = Invite::find($this->inviteId);
        $invite->update([
            'event_id' => $this->eventId,
            'user_id' => $this->userId,
            'invitee_email' => $this->inviteeEmail,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Invite updated successfully.');
        
        return $this->redirect(route('admin.invites'), navigate: true);
    }

    public function render()
    {
        $events = Event::with('calendar.user')->orderBy('title')->get();
        $users = User::orderBy('name')->get();
        
        return view('livewire.admin.invites.edit', [
            'events' => $events,
            'users' => $users,
        ]);
    }
}
