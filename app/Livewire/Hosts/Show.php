<?php

namespace App\Livewire\Hosts;

use App\Models\Host;
use App\Models\Service;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class Show extends Component
{
    public Host $host;
    public $services = [];
    public $scanSuccess = false;

    // Mount method: assign host only
    public function mount(Host $host)
    {
        $this->host = $host;
    }

    // Scan services using Laravel Artisan command
    public function scanServices()
    {
        $exitCode = Artisan::call('scan:services', [
            'host_id' => $this->host->id
        ]);

        if ($exitCode === 0) {
            // Refresh the services list after scanning
            $this->services = Service::where('host_id', $this->host->id)->get();
            $this->scanSuccess = true;
        } else {
            $this->scanSuccess = false;
        }
    }

    // Render view and always fetch the latest services
    public function render()
    {
        $this->services = Service::where('host_id', $this->host->id)->get();
        return view('livewire.hosts.show');
    }
}
