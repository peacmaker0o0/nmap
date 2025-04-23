<div class="px-6 sm:px-8 lg:px-12 max-w-7xl mx-auto space-y-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 text-center">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Total Hosts</h4>
            <p class="text-4xl font-bold text-blue-500">{{ $totalHosts }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 text-center">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Total Ranges</h4>
            <p class="text-4xl font-bold text-green-500">{{ $totalRanges }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 text-center">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Total Services</h4>
            <p class="text-4xl font-bold text-yellow-500">{{ $totalServices }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Hosts Per Range</h3>
            <canvas id="hostsPerRangeChart" height="300"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Top Ports</h3>
            <canvas id="topPortsChart" height="300"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Top Services</h3>
        <canvas id="topServicesChart" height="300"></canvas>
    </div>

    <!-- Service Monitoring Section -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Service Monitoring</h3>

        @foreach($monitorResults as $ip => $result)
            <div class="mb-6 border-t border-gray-300 pt-4">
                <h4 class="text-lg font-semibold text-blue-500">{{ $ip }}</h4>

                @if(!empty($result['down']))
                    <p class="text-red-500 mt-2 font-medium">Services Down:</p>
                    <ul class="list-disc list-inside text-sm text-red-300">
                        @foreach($result['down'] as $service)
                            <li>{{ $service->name }} ({{ $service->port }}/{{ $service->protocol }})</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-green-500 mt-2">No services down 🎉</p>
                @endif

                @if(!empty($result['still_up']))
                    <p class="text-green-500 mt-4 font-medium">Services Still Up:</p>
                    <ul class="list-disc list-inside text-sm text-green-300">
                        @foreach($result['still_up'] as $service)
                            <li>{{ $service->name }} ({{ $service->port }}/{{ $service->protocol }})</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>


    <!-- Add this section after the Service Monitoring section -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Host Uptime Statistics</h3>

    @foreach($uptimeStats as $ip => $stats)
        <div class="mb-8 border-t border-gray-300 pt-4">
            <h4 class="text-lg font-semibold text-blue-500 mb-3">{{ $ip }}</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Uptime/Downtime Cards -->
                <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">Uptime</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-300">
                        {{ $stats['uptime_percentage'] }}%
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-300">
                        {{ gmdate('H\h i\m s\s', $stats['total_uptime_seconds']) }}
                    </p>
                </div>
                
                <div class="bg-red-100 dark:bg-red-900 p-4 rounded-lg">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">Downtime</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-300">
                        {{ $stats['downtime_percentage'] }}%
                    </p>
                    <p class="text-xs text-red-600 dark:text-red-300">
                        {{ gmdate('H\h i\m s\s', $stats['total_downtime_seconds']) }}
                    </p>
                </div>
            </div>
            
            <!-- Timeline Visualization -->
            <div class="mb-4">
                <h5 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Uptime Timeline</h5>
                <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
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
            
            <!-- Detailed Stats -->
            <div class="text-sm">
                <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">First scan:</span> {{ $stats['first_scan']->diffForHumans() }} ({{ $stats['first_scan'] }})
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Last scan:</span> {{ $stats['last_scan']->diffForHumans() }} ({{ $stats['last_scan'] }})
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Total monitoring period:</span> {{ gmdate('H\h i\m s\s', $stats['total_time_seconds']) }}
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Total scans:</span> {{ $stats['total_scans'] }}
                </p>
            </div>
        </div>
    @endforeach
</div>


<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Host Uptime Comparison</h3>
    <canvas id="uptimeComparisonChart" height="300"></canvas>
</div>

<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 text-center">
    <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Avg. Uptime</h4>
    <p class="text-4xl font-bold text-purple-500">
        {{ number_format(collect($uptimeStats->pluck('uptime_percentage'))->avg(), 2) }}%
    </p>
</div>
</div>

<script >
    document.addEventListener('DOMContentLoaded', function () {
        const hostsPerRangeLabels = @json($hostsPerRange->pluck('name'));
        const hostsPerRangeData = @json($hostsPerRange->pluck('hosts_count'));

        const topPortsLabels = @json($topPorts->pluck('port'));
        const topPortsData = @json($topPorts->pluck('count'));

        const topServicesLabels = @json($topServices->pluck('name'));
        const topServicesData = @json($topServices->pluck('count'));

        // Hosts Per Range Chart
        const hostsPerRangeCtx = document.getElementById('hostsPerRangeChart').getContext('2d');
        new Chart(hostsPerRangeCtx, {
            type: 'pie',
            data: {
                labels: hostsPerRangeLabels,
                datasets: [{
                    data: hostsPerRangeData,
                    backgroundColor: ['#A7C7E7', '#B7F0C7', '#F8D6A3', '#C8E0D9', '#F1E2E6', 'red'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });

        // Top Ports Chart
        const topPortsCtx = document.getElementById('topPortsChart').getContext('2d');
        new Chart(topPortsCtx, {
            type: 'bar',
            data: {
                labels: topPortsLabels,
                datasets: [{
                    label: 'Count',
                    data: topPortsData,
                    backgroundColor: '#A7C7E7',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `Count: ${tooltipItem.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // Top Services Chart
        const topServicesCtx = document.getElementById('topServicesChart').getContext('2d');
        new Chart(topServicesCtx, {
            type: 'bar',
            data: {
                labels: topServicesLabels,
                datasets: [{
                    label: 'Count',
                    data: topServicesData,
                    backgroundColor: '#B7F0C7',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `Count: ${tooltipItem.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        const uptimeComparisonCtx = document.getElementById('uptimeComparisonChart').getContext('2d');
new Chart(uptimeComparisonCtx, {
    type: 'bar',
    data: {
        labels: @json($uptimeStats->keys()),
        datasets: [{
            label: 'Uptime %',
            data: @json($uptimeStats->pluck('uptime_percentage')),
            backgroundColor: '#10B981',
            borderRadius: 8,
            borderSkipped: false,
        }, {
            label: 'Downtime %',
            data: @json($uptimeStats->pluck('downtime_percentage')),
            backgroundColor: '#EF4444',
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                stacked: true,
                ticks: {
                    font: {
                        size: 14
                    }
                }
            },
            y: {
                stacked: false,
                beginAtZero: true,
                max: 100,
                ticks: {
                    font: {
                        size: 14
                    },
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.raw.toFixed(2) + '%';
                    }
                }
            }
        }
    }
});
    });


   
</script>
