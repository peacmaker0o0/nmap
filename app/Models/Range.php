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


    public function getTopIPs(?int $limit =null): array
    {


        $limit = $limit ?? config('scan.max_hosts');

        // Make sure CIDR is provided and is a valid number
        if (empty($this->cidr) || !is_numeric($this->cidr) || $this->cidr < 0 || $this->cidr > 32) {
            return []; // Invalid CIDR, return empty array
        }

        $ipBase = $this->ip; // Base IP (e.g., 172.0.0.0)
        $cidr = (int) $this->cidr;

        // Calculate the range of IP addresses
        $ipArray = [];
        $baseParts = explode('.', $ipBase);

        // Calculate the start IP based on CIDR
        $start = (ip2long($ipBase) >> (32 - $cidr)) << (32 - $cidr);

        // Generate the IPs
        for ($i = 0; $i < $limit; $i++) {
            $ipArray[] = long2ip($start + $i);
        }

        return $ipArray;
    }
    

    
}
