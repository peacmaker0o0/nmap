<?php
namespace App\Livewire\Hosts;

use App\Models\Host;
use App\Models\Service;
use Livewire\Component;
use App\Services\NmapService;

class Show extends Component
{
    public Host $host;
    public $services = [];
    public $scanSuccess = false;

    // Method to render the view
    public function render()
    {
        return view('livewire.hosts.show');
    }

    // Method to scan the services for the host
    public function scanServices()
    {
        $nmapService = new NmapService($this->host->range);

        // Scan services and update the services list
        $nmapService->scanServices($this->host);

        // Get the updated services for the host
        $this->services = Service::where('host_id', $this->host->id)->get();
        $this->scanSuccess = true;

        // Refresh the view
        $this->dispatch('servicesScanned');
    }

    // Method to load the services when the page is loaded
    public function mount(Host $host)
    {
        $this->host = $host;
        $this->services = Service::where('host_id', $this->host->id)->get();
    }
}
