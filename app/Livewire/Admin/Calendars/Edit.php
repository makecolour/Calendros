<?php

namespace App\Livewire\Admin\Calendars;

use App\Models\Calendar;
use App\Models\User;
use Livewire\Component;

class Edit extends Component
{
    public $calendarId;
    public $userId;
    public $name;
    public $description;
    public $color;
    public $timezone;
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

    public function mount($calendarId)
    {
        $calendar = Calendar::findOrFail($calendarId);
        $this->calendarId = $calendar->id;
        $this->userId = $calendar->user_id;
        $this->name = $calendar->name;
        $this->description = $calendar->description;
        $this->color = $calendar->color;
        $this->timezone = $calendar->timezone;
        $this->isDefault = $calendar->is_default;
    }

    public function save()
    {
        $this->validate();

        $calendar = Calendar::find($this->calendarId);
        $calendar->update([
            'user_id' => $this->userId,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'timezone' => $this->timezone,
            'is_default' => $this->isDefault,
        ]);

        session()->flash('message', 'Calendar updated successfully.');
        
        return $this->redirect(route('admin.calendars'), navigate: true);
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        
        return view('livewire.admin.calendars.edit', [
            'users' => $users,
        ]);
    }
}
