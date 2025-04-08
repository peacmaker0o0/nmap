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
}
