<?php

namespace App\Livewire\Ranges;

use App\Models\Range;
use Livewire\Component;

class Create extends Component
{


    public string $name;
    public string $ip;
    public ?string $cidr = null;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'ip' => 'required|ip',
        'cidr' => 'nullable|integer|min:0|max:32',
    ];
    public function save()
    {
        Range::firstOrCreate([
            'name'=> $this->name,
            'ip' => $this->ip,
            'cidr' => $this->cidr
        ]);

        return redirect(route('ranges.index'))->with('success','Range Created Successfully');
    }

    public function render()
    {
        return view('livewire.ranges.create');
    }
}
