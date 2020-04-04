<?php

namespace App\Events;

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
    public function __construct(Voting $voting)
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
        $prevotes = $this->voting->participants()->where('vote', '!=', null)->get(['vote']);

        $votes = array_fill(0, $this->voting->variants->count(), 0);

        foreach ($prevotes as $el) {
            $el = explode(',', $el['vote']);
            foreach ($el as $item)
                $votes[$item]++;
        }

        return [
            'voting' => $this->voting,
            'votes' => $votes,
        ];
    }
}
