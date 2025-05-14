<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Host;
use App\Models\Range;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;
    public $totalHosts;
    public $totalRanges;
    public $totalServices;
    public $hostsPerRange;
    public $topPorts;
    public $topServices;

    public $monitorResults;
    public $uptimeStats;

    public $hostVulnerabilities; // Add this property

    public function mount()
    {
        // Existing stats
        $this->totalHosts = Host::count();
        $this->totalRanges = Range::count();
        $this->totalServices = Service::count();

        $this->hostsPerRange = Range::select('id', 'name')
            ->withCount('hosts')
            ->get();

        $this->topPorts = Service::select('port', DB::raw('count(*) as count'))
            ->groupBy('port')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $this->topServices = Service::select('name', DB::raw('count(*) as count'))
            ->groupBy('name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Monitor results
        $this->monitorResults = Host::with('services')->get()->mapWithKeys(function ($host) {
            return [
                $host->ip => $host->monitor(),
            ];
        });

        // Uptime stats for top 5 hosts with most scans
        $this->uptimeStats = Host::withCount('scanHistories')
            ->whereHas('scanHistories')
            ->orderByDesc('scan_histories_count')
            ->limit(5)
            ->get()
            ->mapWithKeys(function ($host) {
                return [$host->ip => $host->uptime()];
            });

        // Fetch vulnerabilities for host with ID 1
        $this->hostVulnerabilities = Host::all()->map(function ($host) {
    return $host->vulnView();
});
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}