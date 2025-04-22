@props(['host'])

<div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition max-w-sm w-full mx-auto">
    <div class="flex items-start justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 truncate">
            {{ $host->domain }}
        </h3>
    </div>

    <div class="text-sm text-gray-700 space-y-2">
        <p>
            <span class="font-medium">IP:</span> {{ $host->ip }}
        </p>
        <p>
            <span class="font-medium">Created:</span> {{ $host->created_at->diffForHumans() }}
        </p>
    </div>

    <div class="flex justify-end gap-2">
        <a wire:navigate href="{{ route('hosts.show', $host) }}">
            <flux:icon.viewfinder-circle/>
        </a>

        <button class="cursor-pointer" wire:click="deleteHost({{ $host->id }})" >
            <flux:icon.trash/>
        </button>
        



    </div>
</div>
