<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;

class Edit extends Component
{
    public $userId;
    public $name;
    public $email;
    public $is_admin = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'is_admin' => 'boolean',
        ];
    }

    public function mount($userId)
    {
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = $user->is_admin;
    }

    public function save()
    {
        $this->validate();

        $user = User::find($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
        ]);

        session()->flash('message', 'User updated successfully.');
        
        return $this->redirect(route('admin.users'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.users.edit');
    }
}
