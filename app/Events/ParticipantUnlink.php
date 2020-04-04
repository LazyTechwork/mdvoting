<?php

namespace App\Events;

use App\Device;
use App\Voting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantUnlink implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $device;
    public $voting;

    /**
     * Create a new event instance.
     *
     * @param Voting $voting
     * @param Device $device
     */
    public function __construct(Voting $voting, Device $device)
    {
        $this->voting = $voting;
        $this->device = $device;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->voting->code);
    }

    public function broadcastAs()
    {
        return 'participantunlink';
    }

    public function broadcastWith()
    {
        return [
            'device' => $this->device
        ];
    }
}
