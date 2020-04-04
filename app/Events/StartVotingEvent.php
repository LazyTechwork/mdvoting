<?php

namespace App\Events;

use App\Device;
use App\Voting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartVotingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;
    private $voting;

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
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->voting->code);
    }

    public function broadcastAs()
    {
        return 'startvoting';
    }

    public function broadcastWith()
    {
        return [
            'device' => $this->device,
            'devices' => Device::all(),
            'participants' => $this->voting->participants()->where('vote', null)->get()->groupBy('group')
        ];
    }
}
