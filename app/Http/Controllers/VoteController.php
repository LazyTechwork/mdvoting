<?php

namespace App\Http\Controllers;

use App\Device;
use App\Events\EndVotingEvent;
use App\Events\ParticipantLinkedDevice;
use App\Events\StartVotingEvent;
use App\Participant;
use App\Voting;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function participantLink(Request $request)
    {
        $participant = Participant::whereId($request->get('p'))->first();
        if ($participant->vote !== null) return response()->json(['status' => 'forbidden'])->setStatusCode(403);

        $voting = Voting::whereId($request->get('v'))->first();
        $device = Device::whereId($request->get('d'))->first();
        $device->update(['status' => 'busy']);
        event(new ParticipantLinkedDevice($voting, $device, $participant));

        return response()->json(['status' => 'ok'])->setStatusCode(200);
    }

    public function startVoting(Request $request)
    {
        $voting = Voting::whereId($request->get('v'))->first();
        $device = Device::whereId($request->get('d'))->first();
        $device->update(['status' => 'voting']);
        event(new StartVotingEvent($voting, $device));

        return response()->json(['items' => $voting->variants, 'maxvotes' => $voting->maxVotes])->setStatusCode(200);
    }

    public function endVoting(Request $request)
    {
        $participant = Participant::whereId($request->get('p'))->first();
        if ($participant->vote === null)
            $participant->update(['vote' => collect($request->get('vote'))->join(',')]);
        $voting = Voting::whereId($request->get('v'))->first();
        $device = Device::whereId($request->get('d'))->first();
        $device->update(['status' => 'free']);
        event(new EndVotingEvent($voting, $device));

        return response()->json(['status' => 'ok'])->setStatusCode(200);
    }

    public function getDevices(Request $request)
    {
        $voting = Voting::whereId($request->get('v'))->first();
        $devices = Device::whereVotingId($voting->id)->get();
        return response()->json(['status' => 'ok', 'items' => $devices, 'count' => $devices->count()]);
    }
}
