<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Calendars</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage all calendars</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.calendars.create') }}" wire:navigate class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                + Create Calendar
            </a>
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
                Back
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('message') }}</p>
        </div>
    @endif

    {{-- Search --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-900">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search calendars..." 
            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
    </div>

    {{-- Calendars Table --}}
    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Default</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($calendars as $calendar)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $calendar->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $calendar->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $calendar->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-block w-6 h-6 rounded" style="background-color: {{ $calendar->color }}"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($calendar->is_default)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">Yes</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $calendar->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('admin.calendars.edit', $calendar->id) }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</a>
                                <button 
                                    type="button"
                                    wire:click="deleteCalendar({{ $calendar->id }})" 
                                    wire:confirm="Are you sure you want to delete this calendar? This action cannot be undone."
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No calendars found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $calendars->links() }}
    </div>
</div>
