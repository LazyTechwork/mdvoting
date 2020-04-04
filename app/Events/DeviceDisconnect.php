<?php

namespace App\Events;

use App\Device;
use App\Voting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceDisconnect implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $voting;
    private $device;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Voting $voting, Device $device)
    {
        $this->voting = $voting;
        $this->device = $device;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->voting->code);
    }

    public function broadcastAs()
    {
        return 'devicedisconnect';
    }

    public function broadcastWith()
    {
        return [
            'device' => $this->device,
            'devices' => $this->voting->devices,
        ];
    }


}
