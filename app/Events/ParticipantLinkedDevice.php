<?php

namespace App\Events;

use App\Device;
use App\Participant;
use App\Voting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantLinkedDevice implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $device;
    public $voting;
    public $participant;

    /**
     * Create a new event instance.
     *
     * @param Voting $voting
     * @param Device $device
     * @param Participant $participant
     */
    public function __construct(Voting $voting, Device $device, Participant $participant)
    {
        $this->voting = $voting;
        $this->device = $device;
        $this->participant = $participant;
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
        return 'participantlinked';
    }
}
