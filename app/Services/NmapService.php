<?php

namespace App\Services;

/**
 * Class NmapService.
 */
class NmapService
{

    public string $ip;


    //scan hosts
    //check status
    //scan services

    public function __construct(string $ip)
    {
        $this->ip = $ip;
    }

    public function scanHosts(): array
    {
        $hosts = [];
        $command = "nmap -sn {$this->ip}";
        
        exec($command, $hosts);

        return $hosts;
    }
}
