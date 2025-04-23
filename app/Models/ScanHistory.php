<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Mail;


class ScanHistory extends Model
{
    protected $guarded = [];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }
    public function vulnerabilities()
    {
        return $this->hasMany(Vulnerability::class);
    }




    public function scopeUp(Builder $query): Builder
    {
        return $query->where("up",true);
    }

    public function scopeDown(Builder $query): Builder
    {
        return $query->where("up",false);
    }



}
