<?php
namespace App\Services;

use App\Models\Host;
use App\Models\Range;

/**
 * Class NmapService.
 */
class NmapService
{
    public string $ip;

    // Constructor
    public function __construct(string $ip)
    {
        $this->ip = $ip;
    }

    // Scan hosts and optionally store them
    public function scanHosts(Range $range, bool $store = false): array
    {
        $hosts = [];
        $command = "nmap -sn {$this->ip}";
        
        exec($command, $output);  // Get the raw output from the command
        
        // Loop through the output and extract relevant data
        foreach ($output as $line) {
            // Match IP addresses and hostnames using regex patterns
            if (preg_match('/Nmap scan report for (.+?) \(([\d\.]+)\)/', $line, $matches)) {
                $domain = $matches[1];  // Hostname
                $ip = $matches[2];  // IP address
                
                // Store the host in the result array
                $hosts[] = ['ip' => $ip, 'domain' => $domain];

                if ($store) {
                    // Check if the host already exists, if not, create it and associate with the provided Range
                    Host::firstOrCreate([
                        'ip' => $ip,
                        'domain' => $domain,
                        'range_id' => $range->id, // Associate the host with the given range
                    ]);
                }
            }
        }

        return $hosts;
    }
}
