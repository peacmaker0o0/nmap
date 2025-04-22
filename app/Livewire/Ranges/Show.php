<?php

namespace App\Livewire\Ranges;

use App\Models\Host;
use App\Models\Range;
use App\Services\NmapService;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public Range $range;
    public string $message = '';
    public string $messageType = 'info'; // 'info', 'success', 'error'

    public function scanHosts()
    {
        $this->message = ''; // Clear any existing messages
        $this->messageType = 'info';
        
        $ns = new NmapService($this->range);
        $result = $ns->scanHosts(store: true);
        
        // If result is string, it's an error message
        if (is_string($result)) {
            $this->message = $result;
            $this->messageType = 'error';
        } else {
            // Scan was successful
            $this->message = count($result) > 0 
                ? 'Scan completed successfully! Found ' . count($result) . ' hosts.' 
                : 'Scan completed, but no hosts were found.';
            $this->messageType = 'success';
            $this->range->refresh();
        }
    }

    
    public function deleteHost(Host $host)
    {
        $host->delete();
        $this->dispatch('host-deleted');
    }

    #[On('host-deleted')]
    public function render()
    {
        return view('livewire.ranges.show');
    }
}