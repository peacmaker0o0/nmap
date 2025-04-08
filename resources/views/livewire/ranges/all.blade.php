<div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($ranges as $range)
            <x-range-card :range="$range" />
        @endforeach
    </div>
</div>
