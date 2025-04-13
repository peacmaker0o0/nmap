<?php

namespace App\Console\Commands;

use App\Models\Range;
use App\Services\NmapService;
use Illuminate\Console\Command;

class ScanHostsForRangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:hosts {range_id}';

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
        $range_id = $this->argument('range_id');
        $range = Range::find($range_id);

        if (!$range) {
            $this->error("Range with ID {$range_id} not found.");
            return 1; // Return a non-zero code to indicate failure
        }
    
        $nmap = new NmapService($range);
    
        // You can now proceed with the scan
        $result = $nmap->scanHosts(store: true);
    
        if (is_string($result)) {
            $this->error("Scan failed: {$result}");
        } else {
            $this->info("Scan completed successfully. Found " . count($result) . " host(s).");
        }
    
        return 0; // Success
    }
    
}
