<?php

namespace App\Http\Controllers;

use App\Participant;
use App\Providers\RouteServiceProvider;
use App\Voting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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

        $voting->update(['name' => $request->get('name'), 'maxVotes' => $request->get('maxVotes')]);

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

        if ($request->has('clear')) {
            Participant::whereVotingId($id)->forceDelete();
            if (Participant::all()->count() == 0) {
                DB::table(with(new Participant)->getTable())->truncate();
            }
            return redirect()->route('votings.participants', ['id' => $id]);
        }

        $validator = validator($request->all(), [
            'list' => ['required', 'max:5000', 'mimes:xlsx,xls,csv'],
            'rewrite' => ['required', 'in:0,1']
        ], [
            'list.required' => 'Файл с участниками обязателен к загрузке',
            'list.max' => 'Файл не должен превышать размер в 5 000 кБ',
            'list.mimes' => 'Файл должен иметь формат Excel (XLSX, XLS) или CSV',
            'rewrite.required' => 'Выберите одно из действий с файлом',
            'rewrite.in' => 'Замечено изменение шаблона HTML'
        ]);

        if ($validator->fails())
            return redirect()->back()->withInput($request->all())->withErrors($validator); // Validating

        $file = $request->file('list'); // Getting file
        $file = $file->move('templists/', Str::random() . '.' . $file->getClientOriginalExtension());
        $needToMerge = !$request->boolean('rewrite'); // Checking is needed to merge or rewrite

        if ($needToMerge) {
            $oldparticipants = Participant::whereVotingId($id)->get(['group', 'name', 'voting_id']); // Getting all participants from DB
            if ($oldparticipants->isEmpty()) {
                $needToMerge = false;
                $oldparticipants = collect();
            }
        } else {
            Participant::whereVotingId($id)->forceDelete();
            if (Participant::all()->count() == 0) {
                DB::table(with(new Participant)->getTable())->truncate();
            }
            $oldparticipants = collect();
        }

//        Creating reader and read Excel file
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $cells = $spreadsheet->getActiveSheet()->getCellCollection();

//        Array of inserted participants
        $participants = [];

//        Map all rows
        for ($i = 1; $i <= $cells->getHighestRow(); $i++) {

            $colA = $cells->get('A' . $i); // Columns
            $colB = $cells->get('B' . $i); // Columns

            if (!$colA || !$colB) // Checking colA and colB on null
                continue;

            $tempParticipant = ['group' => $colB->getValue(), 'name' => $colA->getValue(), 'voting_id' => $id]; // Making temporary participant

            if ($needToMerge) { // If need to merge
                if ($oldparticipants
                    ->where('group', '=', $tempParticipant['group'])
                    ->where('name', '=', $tempParticipant['name'])
                    ->isEmpty()) // Checking is same row in DB
                    $participants[] = $tempParticipant; // Adding to insert query if not in DB
            } else
                $participants[] = $tempParticipant; // If rewrite - writing all participants

        }

        Participant::insert($participants); // Inserting participants
        File::delete($file->getRealPath()); // Removing temp file

        return redirect()->route('votings.participants', ['id' => $voting->id]);
    }

    public function vi()
    {
        return view('vi');
    }

    public function dashboard(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;

        if (!$voting->locked)
            return redirect()->route('votings.show', ['id' => $id])->withErrors(new MessageBag(['action_error' => 'Доступ запрещён, т.к. голосование не заблокировано']));

        return view('votings.dashboard', compact('voting'));
    }

    public function getParticipantsAPI(Request $request)
    {
        $validator = validator($request->all(), [
            'v' => ['required', 'integer', 'exists:votings,id']
        ]);

        if ($validator->fails())
            return response()->json(['status' => 'error'])->setStatusCode(400);

        $voting = Voting::whereId($request->get('v'))->first();
        $participants = $voting->participants()->where('vote', null)->get()->groupBy('group');
        return response()->json(['status' => 'ok', 'items' => $participants->toArray(), 'itemgroups' => $participants->keys()]);
    }

    public function resetVotes(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;
        $voting->participants()->where('vote', '!=', null)->update(['vote' => null]);
        return redirect()->route('votings.show', ['id' => $id]);
    }

    public function voteStats(Request $request, $id)
    {
        $voting = $this->votingCheck($id);
        if (!is_a($voting, Voting::class))
            return $voting;
        return view('votings.stats', compact('voting'));
    }

    public function getVotes(Request $request)
    {
        $voting = Voting::whereId($request->get('v'))->first();

        $prevotes = $voting->participants()->where('vote', '!=', null)->get(['vote']);
        $votes = array_fill(0, $voting->variants->count(), 0);

        foreach ($prevotes as $el) {
            $el = explode(',', $el['vote']);
            foreach ($el as $item)
                $votes[$item]++;
        }

        return response()->json(['status' => 'ok', 'voting' => $voting, 'votes' => $votes])->setStatusCode(200);
    }
}
