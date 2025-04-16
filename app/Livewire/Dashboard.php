<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Host;
use App\Models\Range;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalHosts;
    public $totalRanges;
    public $totalServices;
    public $hostsPerRange;
    public $topPorts;
    public $topServices;

    public function mount()
    {
        $this->totalHosts = Host::count();
        $this->totalRanges = Range::count();
        $this->totalServices = Service::count();

        $this->hostsPerRange = Range::withCount('hosts')->get(['id', 'name']);
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
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
