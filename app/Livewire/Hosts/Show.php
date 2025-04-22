<?php

namespace App\Livewire\Hosts;

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

    public function mount(Host $host)
    {
        $this->host = $host;
    }

    public function scanServices()
    {
        $output = Artisan::call('scan:services', [
            'host_id' => $this->host->id
        ]);
    
        $outputString = Artisan::output();

        info($output);
    
        // if (str_contains($outputString, 'Host seems down')) {
        //     session()->flash('error', 'Host seems down. If it is up, try scanning with -Pn.');
        //     $this->scanSuccess = false;
        //     $this->scanFail = true;
        // } else {
        //     $this->scanSuccess = true;
        //     $this->scanFail = false;
        //     session()->flash('success', 'Services scanned successfully!');
        // }
    
        $this->scanSuccess = true;
        $this->scanFail = false;
        $this->services = Service::where('host_id', $this->host->id)->get();
        $this->dispatch('services-scanned');
    }


   #[On('services-scanned')]
    public function render()
    {
        $this->services = Service::where('host_id', $this->host->id)->get();
        return view('livewire.hosts.show');
    }
}
