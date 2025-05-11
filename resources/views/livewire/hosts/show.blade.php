<div class="container mx-auto p-6">
    <!-- Host Information -->
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Host Details</h1>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <div class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Host Information</div>
        <ul class="space-y-2 text-gray-600 dark:text-gray-300">
            <li><strong class="font-semibold">IP Address:</strong> {{ $host->ip }}</li>
            <li><strong class="font-semibold">Domain:</strong> {{ $host->domain }}</li>
            <li><strong class="font-semibold">Range:</strong> {{ $host->range->name ?? 'N/A' }}</li>
            <li><strong class="font-semibold">Created At:</strong> {{ $host->created_at->diffForHumans() }}</li>
            <li><strong class="font-semibold">Updated At:</strong> {{ $host->updated_at->diffForHumans() }}</li>
        </ul>

        <!-- Scan Services Button -->
        <div class="mt-4">
            <flux:button wire:click="scanServices" wire:loading.attr="disabled" variant="primary">
                Scan Services
            </flux:button>
            <flux:button wire:click="scanVulnerabilities" wire:loading.attr="disabled" variant="primary">
                Scan Vulnerabilities
            </flux:button>
            <span wire:loading wire:target="scanServices" class="ml-2 text-sm text-blue-500 dark:text-blue-300">
                Scanning...
            </span>
        </div>

        @if($scanSuccess)
            <div class="mt-4 px-4 py-3 rounded-md bg-green-100 dark:bg-green-200 text-green-800 border border-green-200 text-sm">
                ✅ Services scanned successfully!
            </div>
        @endif

        @if ($scanFail)
            <div class="mt-4 px-4 py-3 rounded-md bg-red-100 dark:bg-red-200 text-red-800 border border-red-200 text-sm">
                ❌ Scan failed or host seems down. Try using -Pn if it’s blocking pings.
            </div>
        @endif

        @if (session('message'))
            <flux:callout 
                variant="secondary" 
                icon="information-circle" 
                heading="{{ session('message') }}" 
            />
        @endif
    </div>

    <!-- Scan History Tables -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Scan History</h2>

        <!-- Loop through scan history -->
        @foreach ($host->scanHistories as $scan)
            <div class="mb-4">
                <!-- Collapsible Button -->
                <button class="w-full text-left px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md focus:outline-none" 
                        wire:click="toggleScanHistory({{ $scan->id }})">
                    <span class="font-semibold">Scan on {{ $scan->created_at->diffForHumans() }}</span>
                </button>
                
                <!-- Collapsible Table -->
                <div class="{{ $scanHistoryCollapse[$scan->id] ? 'block' : 'hidden' }} mt-4">
                    <table class="min-w-full table-auto text-left border-collapse text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 dark:text-gray-200 dark:bg-gray-700">Port</th>
                                <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 dark:text-gray-200 dark:bg-gray-700">Name</th>
                                <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 dark:text-gray-200 dark:bg-gray-700">Version</th>
                                <th class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 dark:text-gray-200 dark:bg-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scan->services as $service)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2">{{ $service->port }}</td>
                                    <td class="px-4 py-2">{{ $service->name }}</td>
                                    <td class="px-4 py-2">{{ $service->version ?: 'N/A' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs px-2 py-1 rounded-full 
                                            {{ $service->status === 'up' 
                                                ? 'bg-green-200 text-green-800 dark:bg-green-300 dark:text-green-900' 
                                                : 'bg-red-200 text-red-800 dark:bg-red-300 dark:text-red-900' }}">
                                            {{ $service->status === 'up' ? 'Open' : ucfirst($service->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>
