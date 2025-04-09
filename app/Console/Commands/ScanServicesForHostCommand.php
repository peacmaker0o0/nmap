<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScanServicesForHostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:scan {host_id}';

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
        $host_id = $this->argument('name');
        
    }
}
