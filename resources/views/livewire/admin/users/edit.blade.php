<div>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                <a href="{{ route('admin.users') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg">Back</a>
            </div>

            @if (session()->has('message'))
                <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20 mb-6">
                    <p class="text-sm text-green-800 dark:text-green-200">{{ session('message') }}</p>
                </div>
            @endif

            <form wire:submit="save" class="space-y-6 bg-white dark:bg-neutral-900 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" id="name" wire:model="name" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" id="email" wire:model="email" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_admin" wire:model="is_admin" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_admin" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Administrator</label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Save Changes</button>
                </div>
            </form>
        </div>
</div>
