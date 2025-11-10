<?php

namespace App\Livewire\Admin\Calendars;

use App\Models\Calendar;
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

    public function deleteCalendar($calendarId)
    {
        $calendar = Calendar::find($calendarId);
        if ($calendar) {
            $calendar->delete();
            session()->flash('message', 'Calendar deleted successfully.');
        }
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
