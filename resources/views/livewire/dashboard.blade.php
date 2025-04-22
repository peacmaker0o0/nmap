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
</div>

<script>
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
    });
</script>
