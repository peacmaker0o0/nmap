<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Range extends Model
{
    protected $fillable = ['name', 'ip', 'cidr'];

    public function hosts(): HasMany
    {
        return $this->hasMany(Host::class);
    }


    public function getIPCountAttribute()
    {
        // If CIDR is null, treat it as a single IP (like nmap does)
        if ($this->cidr === null) {
            return 1;
        }
    
        $cidr = (int) $this->cidr;
    
        if ($cidr < 0 || $cidr > 32) {
            return 0; // optional: handle invalid CIDR
        }
    
        return pow(2, 32 - $cidr);
    }
    

    
}
