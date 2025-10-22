<?php

namespace App\Livewire\Admin\Calendars;

use App\Models\Calendar;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingCalendarDeletion = false;
    public $calendarIdToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($calendarId)
    {
        $this->confirmingCalendarDeletion = true;
        $this->calendarIdToDelete = $calendarId;
    }

    public function deleteCalendar()
    {
        $calendar = Calendar::find($this->calendarIdToDelete);
        if ($calendar) {
            $calendar->delete();
            session()->flash('message', 'Calendar deleted successfully.');
        }
        
        $this->confirmingCalendarDeletion = false;
        $this->calendarIdToDelete = null;
    }

    public function render()
    {
        $calendars = Calendar::with('user')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.calendars.index', [
            'calendars' => $calendars,
        ]);
    }
}
