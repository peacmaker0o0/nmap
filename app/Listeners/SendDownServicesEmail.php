<?php

namespace App\Listeners;

use App\Events\ScanHistoryCreated;
use App\Mail\DownServicesNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendDownServicesEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScanHistoryCreated $event): void
    {
        $scanHistory = $event->scanHistory;

        // Fetch down services from this scan
        $downServices = $scanHistory->services()->where('status', 'down')->get();

        if ($downServices->isNotEmpty()) {
            Mail::to('jagex879@gmail.com')->send(
                new DownServicesNotification($scanHistory, $downServices)
            );
        }
    }
}
