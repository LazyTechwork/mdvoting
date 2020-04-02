<?php

namespace App\Events;

use App\Device;
use App\Voting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDeviceEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $voting;
    public $device;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Voting $voting, Device $device)
    {
        $this->device = $device;
        $this->voting = $voting;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel($this->voting->code);
    }

    public function broadcastAs()
    {
        return 'newdevice';
    }
}
