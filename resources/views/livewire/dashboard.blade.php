<div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
            <h4 class="text-lg font-semibold mb-2">Total Hosts</h4>
            <p class="text-3xl font-bold text-indigo-600">{{ $totalHosts }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
            <h4 class="text-lg font-semibold mb-2">Total Ranges</h4>
            <p class="text-3xl font-bold text-pink-600">{{ $totalRanges }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
            <h4 class="text-lg font-semibold mb-2">Total Services</h4>
            <p class="text-3xl font-bold text-yellow-600">{{ $totalServices }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Hosts Per Range</h3>
            <canvas id="hostsPerRangeChart" height="250"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Top Ports</h3>
            <canvas id="topPortsChart" height="250"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Top Services</h3>
        <canvas id="topServicesChart" height="250"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hostsPerRangeCtx = document.getElementById('hostsPerRangeChart').getContext('2d');
        const topPortsCtx = document.getElementById('topPortsChart').getContext('2d');
        const topServicesCtx = document.getElementById('topServicesChart').getContext('2d');

        new Chart(hostsPerRangeCtx, {
            type: 'pie',
            data: {
                labels: @json($hostsPerRange->pluck('name')),
                datasets: [{
                    data: @json($hostsPerRange->pluck('hosts_count')),
                    backgroundColor: ['#6366F1', '#EC4899', '#F59E0B', '#10B981', '#3B82F6']
                }]
            }
        });

        new Chart(topPortsCtx, {
            type: 'bar',
            data: {
                labels: @json($topPorts->pluck('port')),
                datasets: [{
                    label: 'Count',
                    data: @json($topPorts->pluck('count')),
                    backgroundColor: '#3B82F6'
                }]
            }
        });

        new Chart(topServicesCtx, {
            type: 'bar',
            data: {
                labels: @json($topServices->pluck('name')),
                datasets: [{
                    label: 'Count',
                    data: @json($topServices->pluck('count')),
                    backgroundColor: '#F59E0B'
                }]
            }
        });
    });
</script>
