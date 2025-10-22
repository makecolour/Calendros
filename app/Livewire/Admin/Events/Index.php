<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingEventDeletion = false;
    public $eventIdToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($eventId)
    {
        $this->confirmingEventDeletion = true;
        $this->eventIdToDelete = $eventId;
    }

    public function deleteEvent()
    {
        $event = Event::find($this->eventIdToDelete);
        if ($event) {
            $event->delete();
            session()->flash('message', 'Event deleted successfully.');
        }
        
        $this->confirmingEventDeletion = false;
        $this->eventIdToDelete = null;
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
