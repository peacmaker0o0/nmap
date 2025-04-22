<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $guarded = [];

    public function scan(): BelongsTo
    {
        return $this->belongsTo(ScanHistory::class);
    }
    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }


    public function scopeDown(Builder $query): Builder
    {
        return $query->where("status","down");
    }

    public function scopeUp(Builder $query): Builder
    {
        return $query->where("status","up");
    }
}
