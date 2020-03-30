<?php

namespace App\Http\Controllers;

use App\Participant;
use App\Providers\RouteServiceProvider;
use App\Voting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

    public function votingCheck($id, $canBeUnlocked = true)
    {
        $voting = Voting::whereId($id)->with('participants')->first();
        if (!$voting->exists() || Auth::id() !== $voting->admin)
            return redirect(RouteServiceProvider::HOME);
        else
            if ((!$canBeUnlocked && $voting->locked))
                return redirect()->route('votings.show', ['id' => $id])->withErrors(new MessageBag(['action_error' => 'Доступ запрещён, т.к. любые изменения заблокированы']));
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
        $voting = $this->votingCheck($id, false);
        if (!is_a($voting, Voting::class))
            return $voting;

        return view('votings.variants', compact('voting'));
    }

    public function variants(Request $request, $id)
    {
        $voting = $this->votingCheck($id, false);
        if (!is_a($voting, Voting::class))
            return $voting;

        $variants = json_decode($request->get('variants')); // Parsing variants from JSON

        foreach ($variants as $key => $variant)
            $variants[$key] = e($variant);        // Clearing from evil

        $voting->update(['variants' => $variants]); // Updating variants

        return redirect()->route('votings.show', ['id' => $id]);
    }

    public function editPage(Request $request, $id)
    {
        $voting = $this->votingCheck($id, false);
        if (!is_a($voting, Voting::class))
            return $voting;
        return view('votings.edit', compact('voting'));
    }

    public function edit(Request $request, $id)
    {
        $voting = $this->votingCheck($id, false);
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

    public function lock(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;

        if ($voting->locked)
            if (!$voting->participants->where('vote', '!=', null)->count())
                $voting->update(['locked' => false]);
            else
                return redirect()->back()->withErrors(new MessageBag(
                    ['action_error' => 'Разлокировка изменений невозможна, т.к. присутствуют проголосовавшие']));
        else
            if (count($voting->variants) <= $voting->maxVotes)
                return redirect()->back()->withErrors(new MessageBag(
                    ['action_error' => 'Блокировка изменений невозможна, т.к. недостаточно вариантов']));
            else
                $voting->update(['locked' => true]);
        return redirect()->route('votings.show', ['id' => $id]);
    }

    public function participantsPage(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;

        return view('votings.participants', compact('voting'));
    }

    public function participants(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;

        $validator = validator($request->all(), [
            'list' => ['required', 'max:5000', 'mimes:xlsx,xls,csv'],
            'rewrite' => ['required', 'in:0,1']
        ]);
        if ($validator->fails())
            return redirect()->back()->withInput($request->all())->withErrors($validator);
        $file = $request->file('list');
        $file = $file->move('tempLists', Str::random() . '.' . $file->getClientOriginalExtension());
        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $cells = $spreadsheet->getActiveSheet()->getCellCollection();
            $participants = [];
            for ($i = 1; $i <= $cells->getHighestRow(); $i++)
                $participants[] = ['group' => $cells->get('B' . $i)->getValue(), 'name' => $cells->get('A' . $i)->getValue(), 'voting_id' => $id];
            Participant::insert($participants);
            dd($file);
            Storage::delete($file->getRealPath());
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(new MessageBag(['action_error' => 'Undefined error: ' . $e]));
        }
        return redirect()->route('votings.participants', ['id' => $voting->id]);
    }
}
