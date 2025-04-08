<?php

namespace App\Livewire\Ranges;

use App\Models\Range;
use Livewire\Component;

class All extends Component
{
    public function render()
    {
        return view('livewire.ranges.all', ['ranges'=> Range::all()]);
    }
}
