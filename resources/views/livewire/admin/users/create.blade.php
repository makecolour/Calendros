<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create User</h1>
        <a href="{{ route('admin.users') }}" wire:navigate class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
            Back to Users
        </a>
    </div>

    <form wire:submit="save" class="space-y-6 bg-white dark:bg-neutral-900 p-8 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
            <input type="text" id="name" wire:model="name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
            <input type="email" id="email" wire:model="email" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('email') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
            <input type="password" id="password" wire:model="password" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            @error('password') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="isAdmin" wire:model="isAdmin" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="isAdmin" class="text-sm font-medium text-gray-700 dark:text-gray-300">Administrator</label>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.users') }}" wire:navigate class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg text-sm font-medium transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                Create User
            </button>
        </div>
    </form>
</div>
