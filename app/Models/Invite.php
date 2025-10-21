<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'invitee_email',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function accept(): void
    {
        $this->update(['status' => 'accepted']);
        
        // Add event to invitee's default calendar with modified title
        $userCalendar = $this->user->calendars()->where('is_default', true)->first();
        
        if ($userCalendar) {
            $eventOwner = $this->event->calendar->user;
            
            $userCalendar->events()->create([
                'title' => $eventOwner->name . "'s " . $this->event->title,
                'description' => $this->event->description,
                'start_time' => $this->event->start_time,
                'end_time' => $this->event->end_time,
                'location' => $this->event->location,
                'is_all_day' => $this->event->is_all_day,
            ]);
        }
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
