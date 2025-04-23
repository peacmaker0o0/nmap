<?php

namespace App\Livewire\Hosts;

use App\Jobs\ScanServicesJob;
use App\Models\Host;
use App\Models\Service;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public Host $host;
    public $services = [];
    public $scanSuccess = false;
    public $scanFail = false;

    // Array to store the collapse state of scan histories
    public $scanHistoryCollapse = [];

    // Initialize the collapse state when the component is mounted
    public function mount(Host $host)
    {
        $this->host = $host;
        // Initialize the collapse state for each scan history (set to false by default)
        $this->scanHistoryCollapse = $this->host->scanHistories->pluck('id', 'id')->mapWithKeys(fn($id) => [$id => false])->toArray();
    }
    // Toggle the collapse state for a specific scan history
    public function toggleScanHistory($scanId)
    {
        $this->scanHistoryCollapse[$scanId] = !($this->scanHistoryCollapse[$scanId] ?? false);
    }

    // Scan services for the host
    public function scanServices()
    {
       ScanServicesJob::dispatch($this->host);
       session()->flash('message','Scanning services is running in background');
       $this->dispatch('scan-started');
      
    }


    private function refreshScanHistoryCollapse()
    {
        // Reinitialize the collapse state for all scan histories
        $this->scanHistoryCollapse = $this->host->scanHistory->pluck('id', 'id')->mapWithKeys(fn($id) => [$id => false])->toArray();
    }

    #[On('scan-started')]
    public function render()
    {
        // Refresh the list of services after scanning
        $this->services = Service::where('host_id', $this->host->id)->get();
        return view('livewire.hosts.show');
    }
}