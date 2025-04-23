@props(['host'])

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition max-w-sm w-full mx-auto">
    <div class="flex items-start justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
            {{ $host->domain }}
        </h3>
    </div>

    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
        <p>
            <span class="font-medium">IP:</span> {{ $host->ip }}
        </p>
        <p>
            <span class="font-medium">Created:</span> {{ $host->created_at->diffForHumans() }}
        </p>
    </div>

    <div class="flex justify-end gap-2 mt-4">
        <a wire:navigate href="{{ route('hosts.show', $host) }}">
            <flux:icon.viewfinder-circle class="text-gray-700 dark:text-gray-300"/>
        </a>

        <button class="cursor-pointer" wire:click="deleteHost({{ $host->id }})">
            <flux:icon.trash class="text-red-600 dark:text-red-400"/>
        </button>
    </div>
</div>
