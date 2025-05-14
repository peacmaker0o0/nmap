<?php

namespace App\Console\Commands;

use App\Models\Host;
use Illuminate\Console\Command;

class CheckUptimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:host-uptime {host_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store the uptime record for host or all hosts in the database';

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

        $this->info("Scanning hosts");
        foreach ($hosts as $host) {
            $status = $host->checkUpTime();
           $this->info("The host {$host->name} is " . ($status ? 'Up' : 'Down'));
        }
    }
}
