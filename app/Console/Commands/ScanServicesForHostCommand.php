<?php

namespace App\Console\Commands;

use App\Models\Host;
use App\Services\NmapService;
use Illuminate\Console\Command;

class ScanServicesForHostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:services {host_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()

    {
        $host_id = $this->argument('host_id');
        
        $host = Host::where('id', $host_id)->first();


        if (!$host) {
            $this->error('Host not found');
            return;
        }

        $range = $host->range;
        info("Scanning host {$host->ip}");
        $nmap = new NmapService($range);
        if($nmap->scanServices($host))
        {
            $this->info('Services scanned');
        }


        
    }
}
