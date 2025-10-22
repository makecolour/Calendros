<?php

namespace App\Livewire\Admin\Invites;

use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public $eventId = '';
    public $userId = '';
    public $inviteeEmail = '';
    public $status = 'pending';

    protected function rules()
    {
        return [
            'eventId' => 'required|exists:events,id',
            'userId' => 'required|exists:users,id',
            'inviteeEmail' => 'required|email',
            'status' => 'required|in:pending,accepted,rejected',
        ];
    }

    public function save()
    {
        $this->validate();

        Invite::create([
            'event_id' => $this->eventId,
            'user_id' => $this->userId,
            'invitee_email' => $this->inviteeEmail,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Invite created successfully.');
        
        return $this->redirect(route('admin.invites'), navigate: true);
    }

    public function render()
    {
        $events = Event::with('calendar.user')->orderBy('title')->get();
        $users = User::orderBy('name')->get();
        
        return view('livewire.admin.invites.create', [
            'events' => $events,
            'users' => $users,
        ]);
    }
}
