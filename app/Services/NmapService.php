<?php

namespace App\Services;

use App\Models\Host;
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
     * Scan hosts in the given range and optionally store them.
     */
    public function scanHosts(bool $store = false): array
    {
        $hosts = [];

        // Handle case: if CIDR is null, just use the IP
        $target = $this->range->cidr
            ? "{$this->range->ip}/{$this->range->cidr}"
            : $this->range->ip;

        $command = "nmap -sn {$target}";
        exec($command, $output);

        foreach ($output as $line) {
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
                    Host::firstOrCreate([
                        'ip' => $ip,
                        'domain' => $domain,
                        'range_id' => $this->range->id,
                    ]);
                }
            }
        }

        return $hosts;
    }


    public function scanServices()
    {
        return null;
    }
}
