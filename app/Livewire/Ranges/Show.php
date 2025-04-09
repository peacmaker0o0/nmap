<?php

namespace App\Livewire\Ranges;

use App\Models\Range;
use App\Services\NmapService;
use Livewire\Component;

class Show extends Component
{

    public Range $range;
    public function scanHosts()
    {

      $ns = new NmapService($this->range);
      $ns->scanHosts(store: true);
      $this->range->refresh(); 

    }


    public function render()
    {
        return view('livewire.ranges.show');
    }
}
