<?php
namespace App\Livewire\Hosts;

use App\Models\Host;
use App\Models\Service;
use Illuminate\Support\Facades\Artisan;
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
        // Assuming you want to pass a specific host_id (e.g., 1)
        $hostId = 1; // You can dynamically get this value based on your requirements
    
        // Execute the Laravel command 'scan:services' programmatically
        $exitCode = Artisan::call('scan:services', [
            'host_id' => $hostId
        ]);
    
        // Check if needed, you can log or return the exit code
        if ($exitCode === 0) {
            return "Scan completed successfully.";
        } else {
            return "An error occurred during the scan.";
        }
    }

    // Method to load the services when the page is loaded
    public function mount(Host $host)
    {
        $this->host = $host;
        $this->services = Service::where('host_id', $this->host->id)->get();
    }
}
