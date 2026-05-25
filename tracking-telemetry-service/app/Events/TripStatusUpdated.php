<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tripId;
    public $status;
    public $data;

    public function __construct(string $tripId, string $status, array $data = [])
    {
        $this->tripId = $tripId;
        $this->status = $status;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('trip.' . $this->tripId);
    }

    public function broadcastAs()
    {
        return 'trip.updated';
    }
}
