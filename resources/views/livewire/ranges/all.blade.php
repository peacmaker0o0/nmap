<div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Justify-end wrapper for the button -->
    <div class="flex justify-end mb-4">
        <a wire:navigate href="{{ route('ranges.create') }}"><flux:button icon="plus" variant="primary"></flux:button></a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($ranges as $range)
            <x-range-card :key="$range->id" :range="$range" />
        @endforeach
    </div>
</div>
