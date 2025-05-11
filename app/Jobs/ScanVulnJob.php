<?php

namespace App\Jobs;

use App\Models\Host;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class ScanVulnJob implements ShouldQueue
{
    use Queueable;

    public Host $host;

    /**
     * Create a new job instance.
     */
    public function __construct(Host $host)
    {
        $this->host = $host;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info("Scanning vulnerabilities in background started");

        $output = Artisan::call('scan:vuln', [
            'host_id' => $this->host->id
        ]);
    
        $outputString = Artisan::output();

        
        info($output);
    }
}
