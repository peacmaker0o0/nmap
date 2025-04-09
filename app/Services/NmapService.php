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
     * Run a command with a timeout in seconds.
     * Default timeout is 60 seconds.
     */
    public function runCommand(string $command, int $timeout = 60): string
    {
        // Execute the command with a timeout
        $descriptors = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"],  // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);
        if (is_resource($process)) {
            // Set a timer for the timeout duration
            $startTime = time();
            $output = '';
            while (true) {
                $output .= fgets($pipes[1]);
                if ((time() - $startTime) >= $timeout) {
                    // Timeout exceeded
                    proc_terminate($process);
                    return "Command timed out.";
                }
                // Check if process is still running and output is being generated
                if (feof($pipes[1])) {
                    break;
                }
            }
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            return $output;
        }

        return "Failed to run command.";
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

        // Use the runCommand function with timeout set to 60 seconds
        $output = $this->runCommand($command, 60);

        if ($output === "Command timed out.") {
            return [];  // Return an empty array if timeout occurred
        }

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

    public function scanServices()
    {
        return null;
    }
}
