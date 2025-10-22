<?php

namespace App\Livewire\Admin\Calendars;

use App\Models\Calendar;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public $userId = '';
    public $name = '';
    public $description = '';
    public $color = '#3b82f6';
    public $timezone = 'UTC';
    public $isDefault = false;

    protected function rules()
    {
        return [
            'userId' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'timezone' => 'required|string',
            'isDefault' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        Calendar::create([
            'user_id' => $this->userId,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'timezone' => $this->timezone,
            'is_default' => $this->isDefault,
        ]);

        session()->flash('message', 'Calendar created successfully.');
        
        return $this->redirect(route('admin.calendars'), navigate: true);
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        
        return view('livewire.admin.calendars.create', [
            'users' => $users,
        ]);
    }
}
