<?php

namespace App\Services;

use App\Models\Host;

/**
 * Class HostService.
 */
class HostService
{

    public Host $host;
    public function __construct(Host $host)
    {
        $this->host = $host;
    }



    public function checkUpTime(): bool
    {
        $status = ping($this->host->ip);
        
        $this->host->uptimes()->create([
            'up'=>$status
        ]);

        return $status;

    }
}
