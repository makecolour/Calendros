<?php

namespace App\Livewire\Admin\Invites;

use App\Models\Invite;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $confirmingInviteDeletion = false;
    public $inviteIdToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($inviteId)
    {
        $this->confirmingInviteDeletion = true;
        $this->inviteIdToDelete = $inviteId;
    }

    public function deleteInvite()
    {
        $invite = Invite::find($this->inviteIdToDelete);
        if ($invite) {
            $invite->delete();
            session()->flash('message', 'Invite deleted successfully.');
        }
        
        $this->confirmingInviteDeletion = false;
        $this->inviteIdToDelete = null;
    }

    public function render()
    {
        $invites = Invite::with(['event.calendar.user', 'user'])
            ->when($this->search, function ($query) {
                $query->where('invitee_email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('event', function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.invites.index', [
            'invites' => $invites,
        ]);
    }
}
