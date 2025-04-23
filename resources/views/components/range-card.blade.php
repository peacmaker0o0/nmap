@props(['range'])

<div {{ $attributes->merge(['class' => 'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition']) }}>
    <div>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $range->name }}</h2>
        
        <dl class="mt-3 text-sm text-gray-700 dark:text-gray-300 space-y-1">
            <div>
                <dt class="font-medium inline">IP:</dt>
                <dd class="inline ml-1">{{ $range->ip }}</dd>
            </div>
            <div>
                <dt class="font-medium inline">CIDR:</dt>
                <dd class="inline ml-1">{{ $range->cidr ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    <div class="flex justify-end gap-2 mt-4">
        <a wire:navigate href="{{ route('ranges.show', $range) }}">
            <flux:icon.viewfinder-circle class="text-gray-700 dark:text-gray-300"/>
        </a>

        <a wire:navigate href="{{ route('ranges.edit', $range) }}" class="cursor-pointer">
            <flux:icon.pencil-square class="text-gray-700 dark:text-gray-300"/>
        </a>

        <button class="cursor-pointer" wire:click="deleteRange({{ $range->id }})">
            <flux:icon.trash class="text-red-600 dark:text-red-400"/>
        </button>
    </div>
</div>
