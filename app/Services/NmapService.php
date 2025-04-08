<?php

namespace App\Services;

use App\Models\Host;

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
    public function scanHosts(bool $store = false): array
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
                    // Check if the host already exists, if not, create it
                    Host::firstOrCreate([
                        'ip' => $ip,
                        'domain' => $domain,
                    ]);
                }
            }
        }

        return $hosts;
    }
}
