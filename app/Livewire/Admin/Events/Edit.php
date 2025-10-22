<?php

namespace App\Livewire\Admin\Events;

use App\Models\Calendar;
use App\Models\Event;
use Livewire\Component;

class Edit extends Component
{
    public $eventId;
    public $calendarId;
    public $title;
    public $description;
    public $startTime;
    public $endTime;
    public $location;
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

    public function mount($eventId)
    {
        $event = Event::findOrFail($eventId);
        $this->eventId = $event->id;
        $this->calendarId = $event->calendar_id;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->startTime = $event->start_time->format('Y-m-d\TH:i');
        $this->endTime = $event->end_time->format('Y-m-d\TH:i');
        $this->location = $event->location;
        $this->isAllDay = $event->is_all_day;
    }

    public function save()
    {
        $this->validate();

        $event = Event::find($this->eventId);
        $event->update([
            'calendar_id' => $this->calendarId,
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'location' => $this->location,
            'is_all_day' => $this->isAllDay,
        ]);

        session()->flash('message', 'Event updated successfully.');
        
        return $this->redirect(route('admin.events'), navigate: true);
    }

    public function render()
    {
        $calendars = Calendar::with('user')->orderBy('name')->get();
        
        return view('livewire.admin.events.edit', [
            'calendars' => $calendars,
        ]);
    }
}
