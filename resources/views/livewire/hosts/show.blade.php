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
            <button wire:click="scanServices" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                Scan Services
            </button>
        </div>

        @if($scanSuccess)
            <div class="mt-4 text-green-600 font-medium">
                Services scanned successfully!
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
                                {{ $service->status === 'open' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                {{ ucfirst($service->status) }}
                            </span>
                        </td>
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
