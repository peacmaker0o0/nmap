<div class="container mx-auto p-6">
    <!-- Host Information -->
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Host Details</h1>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="text-xl font-semibold text-gray-700 mb-4">Host Information</div>
        <ul class="space-y-2 text-gray-600">
            <li><strong class="font-semibold">IP Address:</strong> {{ $host->ip }}</li>
            <li><strong class="font-semibold">Domain:</strong> {{ $host->domain }}</li>
            <li><strong class="font-semibold">Range ID:</strong> {{ $host->range_id }}</li>
            <li><strong class="font-semibold">Created At:</strong> {{ $host->created_at }}</li>
            <li><strong class="font-semibold">Updated At:</strong> {{ $host->updated_at }}</li>
        </ul>

        <!-- Scan Services Button -->
        <div class="mt-4">
            <flux:button wire:click="scanServices" wire:loading.attr="disabled" variant="primary">
                Scan Services
            </flux:button>
            <span wire:loading wire:target="scanServices" class="ml-2 text-sm text-blue-500">
                Scanning...
            </span>
        </div>

        @if($scanSuccess)
            <div class="mt-4 px-4 py-3 rounded-md bg-green-100 text-green-800 border border-green-200 text-sm">
                ✅ Services scanned successfully!
            </div>
        @endif

        @if ($scanFail)
        <div class="mt-4 px-4 py-3 rounded-md bg-red-100 text-red-800 border border-red-200 text-sm">
            ❌ Scan failed or host seems down. Try using -Pn if it’s blocking pings.
        </div>
    @endif
    </div>

    <!-- Services Table -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Services</h2>

        <table class="min-w-full table-auto text-left border-collapse">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100">Port</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100">Name</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100">Version</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100">Status</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100">Scan Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm">{{ $service->port }}</td>
                        <td class="px-4 py-2 text-sm">{{ $service->name }}</td>
                        <td class="px-4 py-2 text-sm">{{ $service->version }}</td>
                        <td class="px-4 py-2 text-sm">
                            <span class="text-xs px-2 py-1 rounded-full 
                                {{ $service->status === 'up' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                {{ ucfirst($service->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm">{{ $service->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">No services found for this host.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
