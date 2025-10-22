<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Invite</h1>
        <a href="{{ route('admin.invites') }}" wire:navigate class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
            Back to Invites
        </a>
    </div>

    <form wire:submit="save" class="space-y-6 bg-white dark:bg-neutral-900 p-8 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div>
            <label for="eventId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event</label>
            <select id="eventId" wire:model="eventId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Select Event</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->calendar->user->name }})</option>
                @endforeach
            </select>
            @error('eventId') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="userId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User</label>
            <select id="userId" wire:model="userId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Select User</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('userId') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="inviteeEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invitee Email</label>
            <input type="email" id="inviteeEmail" wire:model="inviteeEmail" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('inviteeEmail') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <select id="status" wire:model="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="rejected">Rejected</option>
            </select>
            @error('status') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.invites') }}" wire:navigate class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg text-sm font-medium transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                Create Invite
            </button>
        </div>
    </form>
</div>
