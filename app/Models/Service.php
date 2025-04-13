<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $guarded = [];
    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }
}
