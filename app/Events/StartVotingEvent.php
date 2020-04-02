<?php

namespace App\Events;

use App\Device;
use App\Participant;
use App\Voting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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
    public function __construct(Voting $voting, Device $device, Participant $participant)
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
        return new PrivateChannel($this->voting->code);
    }

    public function broadcastAs()
    {
        return 'startvoting';
    }
}
