<?php

namespace App\Events;

use App\Device;
use App\Voting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewVoteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $voting;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Voting $voting, Device $device)
    {
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
        return 'newvote';
    }

    public function broadcastWith()
    {
        $prevotes = $this->voting->participants()->where('vote', '!=', null)->get(['vote'])->toArray();
        $votes = [];
        foreach ($prevotes as $prevote) {
            foreach ($prevote as $item) {
                if (!isset($votes[$item])) $votes[$item] = 1; else $votes[$item]++;
            }
        }
        return [
            'voting' => $this->voting,
            'votes' => $votes,
        ];
    }
}
