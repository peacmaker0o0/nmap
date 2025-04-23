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

    public function scanHistories(): HasMany
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





    public function uptime(): array
    {
        $scanHistories = $this->scanHistories()
            ->orderBy('created_at')
            ->get();
    
        if ($scanHistories->isEmpty()) {
            return [
                'total_scans' => 0,
                'uptime_percentage' => 0,
                'downtime_percentage' => 0,
                'periods' => [],
            ];
        }
    
        $periods = [];
        $previousScan = null;
        $totalUptime = 0;
        $totalDowntime = 0;
        $totalTime = 0;
    
        foreach ($scanHistories as $scan) {
            if ($previousScan) {
                $timeDiff = $scan->created_at->diffInSeconds($previousScan->created_at);
                $totalTime += $timeDiff;
    
                if ($previousScan->up) {
                    $totalUptime += $timeDiff;
                    $status = 'up';
                } else {
                    $totalDowntime += $timeDiff;
                    $status = 'down';
                }
    
                $periods[] = [
                    'status' => $status,
                    'start' => $previousScan->created_at,
                    'end' => $scan->created_at,
                    'duration_seconds' => $timeDiff,
                    'duration_readable' => $scan->created_at->diffForHumans($previousScan->created_at, true),
                ];
            }
    
            $previousScan = $scan;
        }
    
        // Calculate percentages
        $uptimePercentage = $totalTime > 0 ? ($totalUptime / $totalTime) * 100 : 0;
        $downtimePercentage = $totalTime > 0 ? ($totalDowntime / $totalTime) * 100 : 0;
    
        return [
            'total_scans' => $scanHistories->count(),
            'uptime_percentage' => round($uptimePercentage, 2),
            'downtime_percentage' => round($downtimePercentage, 2),
            'total_uptime_seconds' => $totalUptime,
            'total_downtime_seconds' => $totalDowntime,
            'total_time_seconds' => $totalTime,
            'first_scan' => $scanHistories->first()->created_at,
            'last_scan' => $scanHistories->last()->created_at,
            'periods' => $periods,
        ];
    }
    




    public function lastScan(): ScanHistory
    {
        return $this->scanHistory()->orderBy('created_at','desc')->first();
    }


}
