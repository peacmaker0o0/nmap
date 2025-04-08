<?php

namespace App\Livewire\Ranges;

use App\Models\Range;
use Livewire\Component;

class Show extends Component
{

    public Range $range;
    public function render()
    {
        return view('livewire.ranges.show');
    }
}
