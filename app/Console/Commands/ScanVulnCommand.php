<?php

namespace App\Console\Commands;

use App\Models\Host;
use App\Services\NmapService;
use Illuminate\Console\Command;

class ScanVulnCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:vuln {host_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan Vulnerabilities for a given host or all hosts if no host_id is provided';

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
            info("Scanning vuln for host {$host->ip}");
            $this->info("Scanning host {$host->ip}");

            $nmap = new NmapService($range);



            $this->info("Scanning vulns for host {$host->ip}");
            $nmap->vulnScan($host);

            $this->info("Processing host {$host->ip}");
            $nmap->processVuln($host);

  
        }
    }
}
