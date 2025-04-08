<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Host extends Model
{
    protected $fillable = [ 'ip', 'domain'];

    public function range(): BelongsTo
    {
        return $this->belongsTo(Range::class);
    }
}
