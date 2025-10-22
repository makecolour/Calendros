<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Event</h1>
        <a href="{{ route('admin.events') }}" wire:navigate class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
            Back to Events
        </a>
    </div>

    <form wire:submit="save" class="space-y-6 bg-white dark:bg-neutral-900 p-8 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div>
            <label for="calendarId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Calendar</label>
            <select id="calendarId" wire:model="calendarId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Select Calendar</option>
                @foreach ($calendars as $calendar)
                    <option value="{{ $calendar->id }}">{{ $calendar->name }} ({{ $calendar->user->name }})</option>
                @endforeach
            </select>
            @error('calendarId') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
            <input type="text" id="title" wire:model="title" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('title') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea id="description" wire:model="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500"></textarea>
            @error('description') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="startTime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Time</label>
            <input type="datetime-local" id="startTime" wire:model="startTime" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('startTime') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="endTime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Time</label>
            <input type="datetime-local" id="endTime" wire:model="endTime" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('endTime') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location</label>
            <input type="text" id="location" wire:model="location" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('location') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="isAllDay" wire:model="isAllDay" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="isAllDay" class="text-sm font-medium text-gray-700 dark:text-gray-300">All Day Event</label>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.events') }}" wire:navigate class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg text-sm font-medium transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                Create Event
            </button>
        </div>
    </form>
</div>
