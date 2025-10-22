<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Calendar</h1>
        <a href="{{ route('admin.calendars') }}" wire:navigate class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
            Back to Calendars
        </a>
    </div>

    <form wire:submit="save" class="space-y-6 bg-white dark:bg-neutral-900 p-8 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
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
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Calendar Name</label>
            <input type="text" id="name" wire:model="name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea id="description" wire:model="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500"></textarea>
            @error('description') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
            <input type="color" id="color" wire:model="color" class="w-full h-10 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('color') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
            <select id="timezone" wire:model="timezone" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="UTC">UTC</option>
                <option value="America/New_York">America/New_York</option>
                <option value="America/Los_Angeles">America/Los_Angeles</option>
                <option value="Europe/London">Europe/London</option>
                <option value="Asia/Tokyo">Asia/Tokyo</option>
            </select>
            @error('timezone') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="isDefault" wire:model="isDefault" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="isDefault" class="text-sm font-medium text-gray-700 dark:text-gray-300">Set as Default Calendar</label>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.calendars') }}" wire:navigate class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg text-sm font-medium transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                Create Calendar
            </button>
        </div>
    </form>
</div>
