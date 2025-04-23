<?php

namespace App\Jobs;

use App\Models\Host;
use App\Models\Service;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class ScanServicesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public Host $host;

    public $failOnTimeout = true;
    public function __construct(Host $host)
    {
        $this->host = $host;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info("Scanning services in background started");

        $output = Artisan::call('scan:services', [
            'host_id' => $this->host->id
        ]);
    
        $outputString = Artisan::output();

        
        info($output);
    }



    public function retryUntil(): DateTime
    {
        return now()->addMinutes(30);
    }
}
