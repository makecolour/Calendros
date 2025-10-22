<?php

namespace App\Livewire\Admin\Events;

use App\Models\Calendar;
use App\Models\Event;
use Livewire\Component;

class Create extends Component
{
    public $calendarId = '';
    public $title = '';
    public $description = '';
    public $startTime = '';
    public $endTime = '';
    public $location = '';
    public $isAllDay = false;

    protected function rules()
    {
        return [
            'calendarId' => 'required|exists:calendars,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startTime' => 'required|date',
            'endTime' => 'required|date|after:startTime',
            'location' => 'nullable|string|max:255',
            'isAllDay' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        Event::create([
            'calendar_id' => $this->calendarId,
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'location' => $this->location,
            'is_all_day' => $this->isAllDay,
        ]);

        session()->flash('message', 'Event created successfully.');
        
        return $this->redirect(route('admin.events'), navigate: true);
    }

    public function render()
    {
        $calendars = Calendar::with('user')->orderBy('name')->get();
        
        return view('livewire.admin.events.create', [
            'calendars' => $calendars,
        ]);
    }
}
