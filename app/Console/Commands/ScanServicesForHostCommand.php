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
    protected $signature = 'scan:services {host_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan services for a given host or all hosts if no host_id is provided';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host_id = $this->argument('host_id');

        if ($host_id) {
            $hosts = Host::where('id', $host_id)->get();
        } else {
            $this->info('No host_id provided. Scanning all hosts...');
            $hosts = Host::all();
        }

        if ($hosts->isEmpty()) {
            $this->error('No host(s) found.');
            return;
        }

        foreach ($hosts as $host) {
            $range = $host->range;
            info("Scanning host {$host->ip}");
            $this->info("Scanning host {$host->ip}");

            $nmap = new NmapService($range);

            if ($nmap->scan2($host)) {
                $this->info("Services scanned for host {$host->ip}");
            } else {
                $this->warn("Host {$host->ip} seems down");
            }
        }
    }
}
