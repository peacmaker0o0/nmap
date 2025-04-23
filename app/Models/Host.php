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




// In app/Models/Host.php

public function uptime(): array
{
    $scanHistories = $this->scanHistories()->orderBy('created_at')->get();

    if ($scanHistories->isEmpty()) {
        return [
            
            'total_scans' => 0,
            'uptime_percentage' => 0,
            'downtime_percentage' => 0,
            'total_uptime_seconds' => 0,
            'total_downtime_seconds' => 0,
            'total_time_seconds' => 0,
            'first_scan' => null,
            'last_scan' => null,
            'periods' => [],
        ];
    }

    $periods = [];
    $totalUptime = 0;
    $totalDowntime = 0;
    $previousScan = null;

    foreach ($scanHistories as $scan) {
        if ($previousScan) {
            $start = $previousScan->created_at;
            $end = $scan->created_at;
            $duration = $start->diffInSeconds($end);
            $status = $previousScan->up ? 'up' : 'down';

            if ($status === 'up') {
                $totalUptime += $duration;
            } else {
                $totalDowntime += $duration;
            }

            $periods[] = [
                'status' => $status,
                'start' => $start,
                'end' => $end,
                'duration_seconds' => $duration,
                'duration_readable' => gmdate('H:i:s', $duration),
            ];
        }

        $previousScan = $scan;
    }

    // Add time from last scan to now
    $lastScan = $scanHistories->last();
    $lastDuration = $lastScan->created_at->diffInSeconds(now());
    $lastStatus = $lastScan->up ? 'up' : 'down';

    if ($lastStatus === 'up') {
        $totalUptime += $lastDuration;
    } else {
        $totalDowntime += $lastDuration;
    }

    $periods[] = [
        'status' => $lastStatus,
        'start' => $lastScan->created_at,
        'end' => now(),
        'duration_seconds' => $lastDuration,
        'duration_readable' => gmdate('H:i:s', $lastDuration),
    ];

    $totalTime = $totalUptime + $totalDowntime;

    return [
        'domain'=>$this->domain,
        'total_scans' => $scanHistories->count(),
        'uptime_percentage' => $totalTime > 0 ? round(($totalUptime / $totalTime) * 100, 2) : 0,
        'downtime_percentage' => $totalTime > 0 ? round(($totalDowntime / $totalTime) * 100, 2) : 0,
        'total_uptime_seconds' => $totalUptime,
        'total_downtime_seconds' => $totalDowntime,
        'total_time_seconds' => $totalTime,
        'first_scan' => $scanHistories->first()->created_at,
        'last_scan' => $lastScan->created_at,
        'periods' => $periods,
    ];
}





    public function lastScan(): ScanHistory
    {
        return $this->scanHistory()->orderBy('created_at','desc')->first();
    }


}
