<?php

namespace App\Livewire\Ranges;

use App\Models\Range;
use Livewire\Attributes\On;
use Livewire\Component;

class All extends Component
{

    public $ranges;

    public function mount()
    {
        $this->ranges = Range::all();
    }
    public function deleteRange(Range $range): void
    {   
        $range->delete();
        $this->dispatch('range-deleted');
    
    }

    #[On('range-deleted')]
    public function render()
    {
        return view('livewire.ranges.all');
    }
}
