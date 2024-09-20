<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Todo::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:todos',
            'description' => 'nullable',
        ]);

        $todo = Todo::create($request->only('name', 'description'));

        return response()->json($todo, 201);
    }

    public function show($id)
    {
        return Todo::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:todos,name,' . $todo->id . '|max:255',
            'description' => 'nullable',
        ]);

        $todo->update($request->only('name', 'description'));

        return response()->json($todo);
    }

    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);

        $todo->delete();

        return response()->noContent();
    }

    public function markAsComplete($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->is_completed = true;

        $todo->save();

        return response()->json($todo);
    }
}
