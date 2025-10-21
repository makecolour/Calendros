<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'calendar_id' => $this->calendar_id,
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'is_all_day' => $this->is_all_day,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'calendar' => new CalendarResource($this->whenLoaded('calendar')),
            'invites' => InviteResource::collection($this->whenLoaded('invites')),
        ];
    }
}
