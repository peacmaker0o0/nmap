<?php

namespace App\Livewire\Ranges;

use App\Models\Range;
use Livewire\Component;

class Edit extends Component
{
    public string $name = '';
    public string $ip = '';
    public ?string $cidr = null;
    public Range $range;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'ip' => 'required|ip',
        'cidr' => 'nullable|integer|min:0|max:32',
    ];

    public function mount(Range $range)
    {
        $this->range = $range;
        $this->name = $range->name;
        $this->ip = $range->ip;
        $this->cidr = $range->cidr;
    }

    public function save()
    {
        $this->validate();

        $this->range->update([
            'name' => $this->name,
            'ip' => $this->ip,
            'cidr' => $this->cidr,
        ]);
        
        return redirect(route('ranges.all'))->with('success', 'Range Updated Successfully');
    }

    public function render()
    {
        return view('livewire.ranges.create');
    }
}