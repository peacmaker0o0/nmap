

<div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    <div class="bg-white dark:bg-gray-900 shadow mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white text-center">
            Al Ahlia Monitoring System
        </h1>
    </div>
</div>
    <!-- Summary Cards - Made more compact -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300 text-center">
            <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Total Hosts</h4>
            <p class="text-3xl font-bold text-blue-500">{{ $totalHosts }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300 text-center">
            <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Total Ranges</h4>
            <p class="text-3xl font-bold text-green-500">{{ $totalRanges }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300 text-center">
            <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Total Services</h4>
            <p class="text-3xl font-bold text-yellow-500">{{ $totalServices }}</p>
        </div>


        
    </div>

  <!-- First Row of Charts - Side by Side on larger screens -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Hosts Per Range - Set a fixed height for a shorter chart -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Hosts Per Range</h3>
        <div class="relative" style="height: 250px"> <!-- Reduced height for Hosts Per Range -->
            <canvas id="hostsPerRangeChart"></canvas>
        </div>
    </div>



    <!-- Host Uptime Statistics - Set max height and overflow handling -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Host Uptime Statistics</h3>


            <!-- Average Uptime - Compact card -->
    <div class="mb-5 bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300 text-center">
        <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-1">Avg. Uptime</h4>
        <p class="text-3xl font-bold text-purple-500">
            {{ number_format(collect($uptimeStats->pluck('uptime_percentage'))->avg(), 2) }}%
        </p>
    </div>


        <div class="space-y-6 overflow-y-auto" style="max-height: 450px;"> <!-- Set max-height and overflow -->
            @foreach($uptimeStats as $ip => $stats)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="text-md font-medium text-blue-500 mb-2">{{ $stats['domain'] ?? "" }}</h4>
                    <h4 class="text-sm text-gray-500 mb-2">{{ $ip }}</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <!-- Uptime/Downtime Cards - More compact -->
                        <div class="bg-green-50 dark:bg-green-900/50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-green-700 dark:text-green-300">Uptime</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-300">
                                {{ $stats['uptime_percentage'] }}%
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400">
                                {{ gmdate('H\h i\m s\s', $stats['total_uptime_seconds']) }}
                            </p>
                        </div>

                        <div class="bg-red-50 dark:bg-red-900/50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-red-700 dark:text-red-300">Downtime</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-300">
                                {{ $stats['downtime_percentage'] }}%
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400">
                                {{ gmdate('H\h i\m s\s', $stats['total_downtime_seconds']) }}
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Visualization - Made slimmer -->
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Uptime Timeline</h5>
                        <div class="h-4 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            @foreach($stats['periods'] as $period)
                                <div 
                                    class="h-full inline-block" 
                                    style="width: {{ ($period['duration_seconds'] / $stats['total_time_seconds']) * 100 }}%;
                                           background-color: {{ $period['status'] === 'up' ? '#10B981' : '#EF4444' }};"
                                    title="{{ $period['status'] === 'up' ? 'Up' : 'Down' }} for {{ $period['duration_readable'] }} ({{ $period['start'] }} to {{ $period['end'] }})"
                                ></div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Detailed Stats - More compact -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600 dark:text-gray-400">
    <p>
        <span class="font-medium">First scan:</span>
        {{ $stats['first_scan'] ? $stats['first_scan']->diffForHumans() : 'N/A' }}
    </p>
    <p>
        <span class="font-medium">Last scan:</span>
        {{ $stats['last_scan'] ? $stats['last_scan']->diffForHumans() : 'N/A' }}
    </p>
    <p>
        <span class="font-medium">Total period:</span>
        {{ isset($stats['total_time_seconds']) ? gmdate('H\h i\m', $stats['total_time_seconds']) : 'N/A' }}
    </p>
    <p>
        <span class="font-medium">Total scans:</span>
        {{ $stats['total_scans'] ?? 'N/A' }}
    </p>
</div>

                </div>
            @endforeach
        </div>
    </div>
</div>


<!-- Second Row of Charts - 2 Column Grid -->
<!-- Row of Charts - 2 columns on medium and larger screens -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top Services Chart -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Top Services</h3>
        <div class="relative" style="height: 350px">
            <canvas id="topServicesChart"></canvas>
        </div>
    </div>

    <!-- Top Ports Chart -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Top Ports</h3>
        <div class="relative" style="height: 250px">
            <canvas id="topPortsChart"></canvas>
        </div>
    </div>
</div>



<!-- Service Monitoring - Collapsible -->
<div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Service Monitoring</h3>

    <div x-data="{ openHosts: {} }" class="space-y-4">
        @foreach($monitorResults as $ip => $result)
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                <!-- Host Toggle Button -->
                <button 
                    @click="openHosts['{{ $ip }}'] = !openHosts['{{ $ip }}']"
                    class="flex justify-between items-center w-full text-left font-medium text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 focus:outline-none"
                >
                    <span>{{ $ip }}</span>
                    <svg 
                        class="w-5 h-5 transition-transform transform" 
                        :class="{ 'rotate-180': openHosts['{{ $ip }}'] }" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Collapsible Content -->
                <div 
                    x-show="openHosts['{{ $ip }}']" 
                    x-collapse 
                    style="display: none;"
                    class="mt-3 pl-4 border-l-2 border-gray-200 dark:border-gray-600"
                >
                    @php
                        $services = collect($result);
                        $down = $services->where('is_up', false);
                        $up = $services->where('is_up', true);
                    @endphp

                    @if($down->isNotEmpty())
                        <div>
                            <p class="text-sm font-medium text-red-500">Services Down:</p>
                            <ul class="list-disc list-inside text-xs text-red-400 space-y-1 mt-1">
                                @foreach($down as $service)
                                    <li>{{ $service['name'] }} ({{ $service['port'] }}/{{ $service['protocol'] }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-sm text-green-500 mt-1">No services down 🎉</p>
                    @endif

                    @if($up->isNotEmpty())
                        <div class="mt-2">
                            <p class="text-sm font-medium text-green-500">Services Still Up:</p>
                            <ul class="list-disc list-inside text-xs text-green-400 space-y-1 mt-1">
                                @foreach($up as $service)
                                    <li>{{ $service['name'] }} ({{ $service['port'] }}/{{ $service['protocol'] }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

    <!-- Uptime Statistics - Improved layout -->
   



@if($hostVulnerabilities && $hostVulnerabilities->isNotEmpty())
    @foreach($hostVulnerabilities as $index => $hostVuln)
        @if(isset($hostVuln['host']) && isset($hostVuln['vulns']))
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all duration-300 mb-6">
                <button 
                    wire:key="toggle-btn-{{ $loop->index }}" 
                    x-data 
                    @click="$dispatch('toggle-host-vulns-{{ $loop->index }}')" 
                    class="flex justify-between items-center w-full text-left font-semibold text-gray-800 dark:text-white"
                >
                    <span>Vulnerabilities for {{ $hostVuln['host']->domain }} <span class="text-gray-500 text-sm">({{ $hostVuln['host']->ip }})</span>  </span>
                    <svg 
                        class="w-5 h-5 transition-transform transform" 
                        :class="{ 'rotate-180': openHost === {{ $loop->index }} }" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24" 
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Use Alpine.js to control visibility -->
                <div 
                    x-data="{ open: false }" 
                    x-show="open" 
                    x-transition 
                    @toggle-host-vulns-{{ $loop->index }}.window="open = !open"
                    style="display: none;" 
                    class="mt-4"
                >
                    @if($hostVuln['vulns']->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <tr>
                                        <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Port</th>
                                        <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Protocol</th>
                                        <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Vulnerability ID</th>
                                        <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Score</th>
                                        <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Tag</th>
                                        <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hostVuln['vulns'] as $vulnItem)
                                        @if(isset($vulnItem['parsed_vulnerabilities']) && is_array($vulnItem['parsed_vulnerabilities']) && !empty($vulnItem['parsed_vulnerabilities']))
                                            @foreach($vulnItem['parsed_vulnerabilities'] as $vulnerability)
                                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                                    <td class="px-4 py-2">{{ $vulnItem['port'] }}</td>
                                                    <td class="px-4 py-2">{{ $vulnItem['protocol'] }}</td>
                                                    <td class="px-4 py-2 font-medium">{{ $vulnerability['id'] ?? 'N/A' }}</td>
                                                    <td class="px-4 py-2">{{ $vulnerability['score'] ?? 'N/A' }}</td>
                                                    <td class="px-4 py-2">
                                                        @if(isset($vulnerability['tag']))
                                                            <span class="text-red-500 font-semibold">{{ $vulnerability['tag'] }}</span>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        @if(isset($vulnerability['url']))
                                                            <a href="{{ $vulnerability['url'] }}" target="_blank" class="text-blue-500 hover:underline">Details</a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                                <td class="px-4 py-2">{{ $vulnItem['port'] }}</td>
                                                <td class="px-4 py-2">{{ $vulnItem['protocol'] }}</td>
                                                <td class="px-4 py-2 text-gray-500 italic" colspan="4">No vulnerabilities found for this port.</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 mt-2">No vulnerability scan data found for this host.</p>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
@else
    <p class="text-gray-600 dark:text-gray-400 mt-2">No hosts or vulnerabilities found.</p>
@endif





</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize all charts with responsive settings
    const initChart = (id, config) => {
        const ctx = document.getElementById(id).getContext('2d');
        return new Chart(ctx, {
            ...config,
            options: {
                ...config.options,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    ...(config.options.plugins || {}),
                    legend: {
                        ...(config.options.plugins?.legend || {}),
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    };

    // Hosts Per Range - Changed to donut
    initChart('hostsPerRangeChart', {
        type: 'doughnut',
        data: {
            labels: @json($hostsPerRange->pluck('name')),
            datasets: [{
                data: @json($hostsPerRange->pluck('hosts_count')),
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#6366F1', '#EC4899', '#EF4444'],
                borderWidth: 0,
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // Top Ports - Horizontal bar for better readability
    initChart('topPortsChart', {
        type: 'bar',
        data: {
            labels: @json($topPorts->pluck('port')),
            datasets: [{
                label: 'Count',
                data: @json($topPorts->pluck('count')),
                backgroundColor: '#3B82F6',
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Top Services - Horizontal bar
    initChart('topServicesChart', {
        type: 'bar',
        data: {
            labels: @json($topServices->pluck('name')),
            datasets: [{
                label: 'Count',
                data: @json($topServices->pluck('count')),
                backgroundColor: '#10B981',
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Uptime Comparison Chart
    initChart('uptimeComparisonChart', {
        type: 'bar',
        data: {
            labels: @json($uptimeStats->keys()),
            datasets: [{
                label: 'Uptime %',
                data: @json($uptimeStats->pluck('uptime_percentage')),
                backgroundColor: '#10B981',
                borderRadius: 4,
            }, {
                label: 'Downtime %',
                data: @json($uptimeStats->pluck('downtime_percentage')),
                backgroundColor: '#EF4444',
                borderRadius: 4,
            }]
        },
        options: {
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>