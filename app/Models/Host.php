<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Host extends Model
{
    protected $fillable = [ 'ip', 'domain'];

    public function range(): BelongsTo
    {
        return $this->belongsTo(Range::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function scanHistory(): HasMany
    {
        return $this->hasMany(ScanHistory::class);
    }


    public function monitor($latestScanServices = [])
    {
        // 1. Get current DB services keyed by name:port
        $existingServices = $this->services->keyBy(function ($service) {
            return $service->name . ':' . $service->port;
        });
    
        // 2. Create collection from latest scan data
        $latestCollection = collect($latestScanServices)->keyBy(function ($s) {
            return $s['name'] . ':' . $s['port'];
        });
    
        // 3. Merge both, marking status appropriately
        $allKeys = $existingServices->keys()->merge($latestCollection->keys())->unique();
    
        return $allKeys->map(function ($key) use ($existingServices, $latestCollection) {
            if ($latestCollection->has($key)) {
                $service = $latestCollection[$key];
                return [
                    'name' => $service['name'],
                    'port' => $service['port'],
                    'protocol' => $service['protocol'],
                    'status' => $service['status'] ?? 'up',
                    'is_up' => $service['status'] === 'up',
                    'last_checked' => now()->diffForHumans(), // or actual timestamp if provided
                ];
            } else {
                $service = $existingServices[$key];
                return [
                    'name' => $service->name,
                    'port' => $service->port,
                    'protocol' => $service->protocol,
                    'status' => 'down',
                    'is_up' => false,
                    'last_checked' => now()->diffForHumans(), // or use $service->updated_at
                ];
            }
        })->values(); // optional: remove keys
    }


}
