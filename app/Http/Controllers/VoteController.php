<?php

namespace App\Http\Controllers;

use App\Device;
use App\Events\DeviceDisconnect;
use App\Events\DeviceUnlink;
use App\Events\EndVotingEvent;
use App\Events\NewDeviceEvent;
use App\Events\NewVoteEvent;
use App\Events\ParticipantLinkedDevice;
use App\Events\ParticipantUnlink;
use App\Events\StartVotingEvent;
use App\Participant;
use App\Voting;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function participantLink(Request $request)
    {
        $participant = Participant::whereId($request->get('p'))->first();
        $voting = Voting::whereId($request->get('v'))->first();

        if ($participant->vote !== null) return response()->json(['status' => 'forbidden', 'devices' => $voting->devices])->setStatusCode(403);

        $device = Device::whereId($request->get('d'))->first();

        if ($device->status !== 'free')
            return response()->json(['status' => 'forbidden'])->setStatusCode(403);

        $device->update(['status' => 'busy']);
        event(new ParticipantLinkedDevice($voting, $device, $participant));

        return response()->json(['status' => 'ok', 'devices' => $voting->devices])->setStatusCode(200);
    }

    public function cancelParticipantLink(Request $request)
    {
        $device = Device::whereId($request->get('d'))->first();
        $voting = Voting::whereId($request->get('v'))->first();

        if ($device->status === 'free')
            return response()->json(['status' => 'ok', 'devices' => $voting->devices, 'participants' => $voting->participants()->where('vote', null)->get()])->setStatusCode(200);

        $device->update(['status' => 'free']);
        event(new ParticipantUnlink($voting, $device));

        return response()->json(['status' => 'ok', 'devices' => $voting->devices, 'participants' => $voting->participants()->where('vote', null)->get()])->setStatusCode(200);
    }

    public function startVoting(Request $request)
    {
        $voting = Voting::whereCode($request->get('v'))->first();
        $device = Device::whereId($request->get('d'))->first();
        $device->update(['status' => 'voting']);
        event(new StartVotingEvent($voting, $device));

        return response()->json(['status' => 'ok', 'items' => $voting->variants, 'maxvotes' => $voting->maxVotes])->setStatusCode(200);
    }

    public function endVoting(Request $request)
    {
        $participant = Participant::whereId($request->get('p'))->first();
        $voting = Voting::whereCode($request->get('v'))->first();
        if ($participant->vote === null)
            $participant->update(['vote' => collect($request->get('vote'))->slice(0, $voting->maxVotes)->join(',')]);
        $device = Device::whereId($request->get('d'))->first();
        $device->update(['status' => 'free']);
        event(new EndVotingEvent($voting, $device));
        event(new NewVoteEvent($voting));

        return response()->json(['status' => 'ok'])->setStatusCode(200);
    }

    public function getDevices(Request $request)
    {
        $voting = Voting::whereId($request->get('v'))->first();
        $devices = Device::whereVotingId($voting->id)->get();
        return response()->json(['status' => 'ok', 'items' => $devices, 'count' => $devices->count()]);
    }

    public function connectDevice(Request $request)
    {
        $voting = Voting::whereCode($request->get('vi_code'))->first();
        if (!$voting)
            return response()->json(['status' => 'notfound'])->setStatusCode(404);
        $device = Device::create(['name' => $request->get('vi_name'), 'voting_id' => $voting->id]);
        event(new NewDeviceEvent($voting, $device));
        return response()->json(['status' => 'ok', 'item' => $device]);
    }

    public function deviceUnlink(Request $request)
    {
        $voting = Voting::whereId($request->get('v'))->first();
        $device = Device::whereId($request->get('d'))->first();
        event(new DeviceUnlink($voting, $device));
        $device->forceDelete();

        return response()->json(['status' => 'ok', 'devices' => $voting->devices]);
    }

    public function deviceDisconnect(Request $request)
    {
        $voting = Voting::whereCode($request->get('v'))->first();
        $device = Device::whereId($request->get('d'))->first();
        event(new DeviceDisconnect($voting, $device));
        $device->forceDelete();

        return response()->json(['status' => 'ok'])->setStatusCode(200);
    }
}
