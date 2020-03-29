<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\Voting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function home()
    {
        $votings = collect();
        if (Auth::check())
            $votings = Voting::whereAdmin(Auth::id())->get();
        return view('home', compact('votings'));
    }

    public function newVotingPage()
    {
        return view('votings.new');
    }

    public function newVoting(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'maxVotes' => ['required', 'integer', 'gte:1']
        ], [
            'name.required' => 'Имя голосования обязательно указывать!',
            'name.max' => 'Имя не может превышать 255 символов!',
            'maxVotes.required' => 'Количество голосов обязательно указывать',
            'maxVotes.gte' => 'Количество голосов должно быть больше 0',
        ]);
        if ($validator->fails())
            return redirect()->back()->withInput($request->all())->withErrors($validator);
        $voting = Voting::create([
            'name' => $request->get('name'),
            'admin' => Auth::id(),
            'code' => strtoupper(Str::random(6)),
            'maxVotes' => $request->get('maxVotes'),
            'variants' => []
        ]);
        return redirect()->route('votings.show', ['id' => $voting->id]);
    }

    public function votingCheck($id)
    {
        $voting = Voting::whereId($id)->with('participants')->first();
        if (!$voting->exists() || Auth::id() !== $voting->admin)
            return redirect(RouteServiceProvider::HOME);
        else
            return $voting;
    }

    public function showVoting(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;
        return view('votings.show', compact('voting'));
    }

    public function resetCode(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;
        $voting->code = strtoupper(Str::random(6));
        $voting->save();
        return redirect()->route('votings.show', ['id' => $voting->id]);
    }

    public function variantsPage(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;

        return view('votings.variants', compact('voting'));
    }

    public function variants(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;
        $voting->update(['variants' => json_decode($request->get('variants'))]);

        return redirect()->route('votings.show', ['id' => $id]);
    }

    public function editPage(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;
        return view('votings.edit', compact('voting'));
    }

    public function edit(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;

        $validator = validator($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'maxVotes' => ['required', 'integer', 'gte:1']
        ], [
            'name.required' => 'Имя голосования обязательно указывать!',
            'name.max' => 'Имя не может превышать 255 символов!',
            'maxVotes.required' => 'Количество голосов обязательно указывать',
            'maxVotes.gte' => 'Количество голосов должно быть больше 0',
        ]);
        if ($validator->fails())
            return redirect()->back()->withInput($request->all())->withErrors($validator);

        $voting->update($request->only('name', 'maxVotes'));

        return redirect()->route('votings.show', ['id' => $voting->id]);
    }
}
