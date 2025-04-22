<?php

namespace App\Events;

use App\Models\ScanHistory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScanHistoryCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ScanHistory $scanHistory;

    /**
     * Create a new event instance.
     */
    public function __construct(ScanHistory $scanHistory)
    {
        $this->scanHistory = $scanHistory;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
