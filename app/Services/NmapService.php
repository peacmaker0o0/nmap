<?php
namespace App\Services;

use App\Models\Host;
use App\Models\Service;
use App\Models\Range;

/**
 * Class NmapService
 */
class NmapService
{
    protected Range $range;

    public function __construct(Range $range)
    {
        $this->range = $range;
    }

    /**
     * Run a command with a timeout in seconds.
     * Default timeout is 60 seconds.
     */
    public function runCommand(string $command): string
    {
        $output = [];
        $returnVar = 0;
    
        exec($command . ' 2>&1', $output, $returnVar);
    
        return implode("\n", $output);
    }
    

    /**
     * Scan hosts in the given range and optionally store them.
     * 
     * @param bool $store Whether to store the results
     * @return array|string Returns array of hosts or an error message string
     */
    public function scanHosts(bool $store = false): array|string
    {
        // Check if the number of hosts exceeds the max allowed for scanning
        if ($this->range->ip_count > config('scan.max_hosts')) {
            // Get top IPs if the host count is too large
            $ips = $this->range->getTopIPs(); // Get the top IPs (defaults to 20, or you can pass a custom number)
    
            $hosts = [];
            foreach ($ips as $ip) {
                $command = "nmap -sn {$ip}"; // Scan each IP individually
                $output = $this->runCommand($command);
    
                if ($output === "Command timed out.") {
                    return "Scan timed out. Please try again later.";
                }
    
                // Process output for the current IP
                foreach (explode("\n", $output) as $line) {
                    $domain = null;
    
                    // Try matching with hostname + IP
                    if (preg_match('/Nmap scan report for (.+?) \(([\d\.]+)\)/', $line, $matches)) {
                        $domain = $matches[1];
                        $ip = $matches[2];
                    }
                    // Try matching just an IP
                    elseif (preg_match('/Nmap scan report for ([\d\.]+)/', $line, $matches)) {
                        $ip = $matches[1];
                        $domain = null;
                    }
    
                    if ($ip) {
                        $hosts[] = ['ip' => $ip, 'domain' => $domain];
    
                        if ($store) {
                            $this->range->hosts()->firstOrCreate(
                                ['ip' => $ip],
                                ['domain' => $domain]
                            );
                        }
                    }
                }
            }
    
            return $hosts;
        }
    
        // If the host count is within limits, run the scan normally
        $hosts = [];
        // Handle case: if CIDR is null, just use the IP
        $target = $this->range->cidr
            ? "{$this->range->ip}/{$this->range->cidr}"
            : $this->range->ip;
    
        $command = "nmap -sn {$target}";
        
        // Use the runCommand function with timeout set to 60 seconds
        $output = $this->runCommand($command);
    
        if ($output === "Command timed out.") {
            return "Scan timed out. Please try again later.";
        }
    
        // Process the scan result
        foreach (explode("\n", $output) as $line) {
            $ip = null;
            $domain = null;
    
            // Try matching with hostname + IP
            if (preg_match('/Nmap scan report for (.+?) \(([\d\.]+)\)/', $line, $matches)) {
                $domain = $matches[1];
                $ip = $matches[2];
            }
            // Try matching just an IP
            elseif (preg_match('/Nmap scan report for ([\d\.]+)/', $line, $matches)) {
                $ip = $matches[1];
                $domain = null;
            }
    
            if ($ip) {
                $hosts[] = ['ip' => $ip, 'domain' => $domain];
    
                if ($store) {
                    $this->range->hosts()->firstOrCreate(
                        ['ip' => $ip],
                        ['domain' => $domain]
                    );
                }
            }
        }
    
        return $hosts;
    }

    /**
     * Scan services for a given host.
     */
    public function scanServices(Host $host): bool|string
    {
        $command = "nmap -sS -sV -O -T3 {$host->ip}";
        info("Running Command: $command");
    
        $output = $this->runCommand($command);
        info($output);
    
        if (str_contains($output, 'Host seems down')) {
            return false;
        }
    
        $services = $this->parseNmapOutputForServices($output, $host);
        info("All services parsed");
    
        foreach ($services as $serviceData) {
            info("Updating or creating service", [
                'host_id'  => $host->id,
                'port'     => $serviceData['port'],
                'protocol' => $serviceData['protocol'],
                'name'     => $serviceData['name'],
                'version'  => $serviceData['version'],
                'status'   => $serviceData['status'],
            ]);
    
            Service::create([
                'host_id' => $host->id,
                'port'    => $serviceData['port'],
                'protocol'=> $serviceData['protocol'],
                'name'    => $serviceData['name'],
                'status'  => $serviceData['status'],
                'version' => trim($serviceData['version']),
            ]);
        }
    
        return true;
    }

    /**
     * Parse Nmap output and extract services information.
     */
    public function parseNmapOutputForServices(string $output, Host $host): array
    {
        $services = [];
    
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            // Parse service details from the Nmap output
            // Modify the regex to account for possible variations in spacing or format
            if (preg_match('/^(\d+)\/tcp\s+(open|closed)\s+(\S+)(\s+.+)?$/', $line, $matches)) {
                // Extract service details from the regex match
                $port = $matches[1];
                $state = $matches[2];
                $name = $matches[3];
                $version = isset($matches[4]) ? $matches[4] : null;
    
                // Only store services that are open
                if ($state === 'open') {
                    $services[] = [
                        'host_id' => $host->id,
                        'port' => $port,
                        'protocol' => 'tcp',
                        'name' => $name,
                        'version' => $version,
                        'status' => 'up',  // Mark as "up" since the state is "open"
                    ];
                } elseif ($state === 'closed') {
                    // Optionally, store closed services as "down"
                    $services[] = [
                        'host_id' => $host->id,
                        'port' => $port,
                        'protocol' => 'tcp',
                        'name' => $name,
                        'version' => $version,
                        'status' => 'down',  // Mark as "down" for closed services
                    ];
                }
            }
        }
    
        return $services;
    }
}