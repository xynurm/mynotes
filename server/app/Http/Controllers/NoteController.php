<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $cacheKey = 'notes_' . md5($search) . '_page_' . $page;

        $notes = Cache::remember($cacheKey, 6, function () use ($search, $page) {
            $query = Note::query();

            if ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            }

            return $query->latest()->paginate(10, ['*'], 'page', $page);
        });

        return response()->json($notes,200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $note = Note::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($note, 201);
    }

    public function show($id)
    {
        $note = Note::findOrFail($id);
        return response()->json($note, 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $note = Note::findOrFail($id);
        $note->update($request->all());

        return response()->json($note, 200);
    }

    public function delete($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return response()->json(null, 204);
    }
}
