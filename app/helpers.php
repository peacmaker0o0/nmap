<?php

use Illuminate\Contracts\Filesystem\Filesystem;

function tmp(): Filesystem
{
    return Storage::disk("tmp");
}



function ping(string $ip): bool
{
    $escapedIp = escapeshellarg($ip);
    
    // Detect OS and use appropriate ping command
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = "ping -n 1 -w 2000 $escapedIp";
    } else {
        $command = "ping -c 1 -W 2 $escapedIp";
    }

    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);

    // Some systems return 0 even when packet loss occurs, so check output too
    if ($returnVar === 0) {
        // Check for "100% packet loss" in output (Linux/Windows format)
        $outputStr = implode(' ', $output);
        if (strpos($outputStr, '100% packet loss') !== false || 
            strpos($outputStr, 'Lost = 1') !== false) {
            return false;
        }
        return true;
    }

    return false;
}