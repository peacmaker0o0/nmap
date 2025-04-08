@props(['range'])

<div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition max-w-xs w-full mx-auto">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">{{ $range->name }}</h2>
        
        <dl class="mt-3 text-sm text-gray-700 space-y-1">
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

    <div class="mt-6 text-right">
        <a href="{{ route('ranges.show', $range) }}">
            <button class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Show
            </button>
        </a>
    </div>
</div>
