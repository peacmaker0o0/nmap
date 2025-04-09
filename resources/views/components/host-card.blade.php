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

    <div class="mt-6 text-right">
        <a href="#">
            <button class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Details
            </button>
        </a>
    </div>
</div>
