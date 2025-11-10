<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteEvent($eventId)
    {
        $event = Event::find($eventId);
        if ($event) {
            $event->delete();
            session()->flash('message', 'Event deleted successfully.');
        }
    }

    public function render()
    {
        $events = Event::with('calendar.user')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.events.index', [
            'events' => $events,
        ]);
    }
}
