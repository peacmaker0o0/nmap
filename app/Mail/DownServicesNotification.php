<?php

namespace App\Mail;

use App\Models\ScanHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DownServicesNotification extends Mailable
{
    use Queueable, SerializesModels;

    public ScanHistory $scanHistory;
    public $downServices;

    public function __construct(ScanHistory $scanHistory, $downServices)
    {
        $this->scanHistory = $scanHistory;
        $this->downServices = $downServices;
    }

    public function build()
    {
        return $this->markdown('emails.down_services')
                    ->subject('⚠️ Down Services Detected')
                    ->with([
                        'scanHistory' => $this->scanHistory,
                        'downServices' => $this->downServices,
                    ]);
    }
}
