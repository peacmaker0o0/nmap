<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VulnScanHistory extends Model
{
    public function vulnerabilities(): HasMany
    {
        return $this->hasMany(Vulnerability::class);

    }


    public function host()
    {
        return $this->belongsTo(Host::class);
    }
}
