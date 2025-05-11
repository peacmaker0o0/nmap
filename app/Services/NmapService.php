<?php
namespace App\Services;

use App\Events\ScanHistoryCreated;
use App\Models\Host;
use App\Models\ScanHistory;
use App\Models\Service;
use App\Models\Range;
use App\Models\Vulnerability;

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
                $command = "nmap -Pn -sn {$ip}"; // Scan each IP individually
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
    
        $command = "nmap -Pn -sn {$target}";
        
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
        $command = "nmap -Pn -sS -sV -O -T3 {$host->ip}";
        info("Running Command: $command");
    
        $output = $this->runCommand($command);
        info($output);
    
        if (str_contains($output, 'Host seems down')) {
            return false;
        }
    
        $services = $this->parseNmapOutputForServices($output, $host);
        info("All services parsed");
        $scan = $host->scanHistories()->create();
    
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
                'scan_history_id'=>$scan->id,
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


    public static function scan2(Host $host): bool
    {



        //if host is down, create scan history 
        if (!ping($host->ip)) {
            $scan = $host->scanHistories()->create([
                'up'=>false
            ]);
            return false;
        }


        $resultPath = tmp()->path($host->id) . '.xml';
        $command = "nmap -Pn -sS -sV -O -T3 -oX $resultPath {$host->ip}";
        shell_exec($command);
    
        // Now parse the XML
        self::parseXmlResult($resultPath, $host);
        return true;
    }







    public static function parseXmlResult(string $filePath, Host $host): void
{
    if (!file_exists($filePath)) {
        throw new \Exception("Nmap XML output not found at: $filePath");
    }

    $xml = simplexml_load_file($filePath);
    if (!$xml) {
        throw new \Exception("Failed to parse Nmap XML output.");
    }

    $scan = $host->scanHistories()->create([
        'up'=>true
    ]);



    // Map Nmap states to your allowed DB values
    $statusMap = [
        'open'   => 'up',
        'closed' => 'down',
        'filtered' => 'down',   // Optional mappings
        'unfiltered' => 'down',
    ];

    foreach ($xml->host->ports->port as $port) {
        $portId = (int) $port['portid'];
        $protocol = (string) $port['protocol'];
        $state = (string) $port->state['state'];

        $serviceName = (string) $port->service['name'];
        $serviceVersion = (string) $port->service['version'];
        $serviceProduct = (string) $port->service['product'];

        // Normalize the status using the mapping
        $normalizedStatus = $statusMap[$state] ?? 'unknown';

        // Save service
        Service::create([
            'host_id'          => $host->id,
            'scan_history_id'  => $scan->id,
            'port'             => $portId,
            'protocol'         => $protocol,
            'name'             => $serviceName,
            'version'          => $serviceVersion ?: $serviceProduct, // fallback if version missing
            'status'           => $normalizedStatus,
        ]);
    }

    // Optional: Parse OS info
    if (isset($xml->host->os)) {
        $osMatches = $xml->host->os->osmatch;
        foreach ($osMatches as $os) {
            $osName = (string) $os['name'];
            $host->domain = $osName;
            $host->save();
            $accuracy = (int) $os['accuracy'];
            // Save or use OS info as needed
        }
    }


    event(new ScanHistoryCreated($scan));
}




public static function vulnScan(Host $host): string
{

    
    $command = "nmap -p- -T4 -sV --script vuln -oX {$host->vuln_path}";

    dd($command);
    return $this->runCommand($command);
}



}