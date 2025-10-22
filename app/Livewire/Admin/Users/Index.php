<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingUserDeletion = false;
    public $userIdToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($userId)
    {
        $this->confirmingUserDeletion = true;
        $this->userIdToDelete = $userId;
    }

    public function deleteUser()
    {
        $user = User::find($this->userIdToDelete);
        if ($user) {
            $user->delete();
            session()->flash('message', 'User deleted successfully.');
        }
        
        $this->confirmingUserDeletion = false;
        $this->userIdToDelete = null;
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.users.index', [
            'users' => $users,
        ]);
    }
}
