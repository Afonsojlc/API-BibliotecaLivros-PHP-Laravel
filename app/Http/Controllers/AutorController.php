<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    public function index()
    {
        $autores = Autor::withCount('livros')->paginate(10);
        return response()->json($autores);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'            => 'required|string|max:255',
            'nacionalidade'   => 'nullable|string|max:100',
            'data_nascimento' => 'nullable|date',
            'biografia'       => 'nullable|string',
        ]);

        $autor = Autor::create($validated);
        return response()->json($autor, 201);
    }

    public function show(string $id)
    {
        $autor = Autor::with('livros')->findOrFail($id);
        return response()->json($autor);
    }

    public function update(Request $request, string $id)
    {
        $autor = Autor::findOrFail($id);
        $validated = $request->validate([
            'nome'            => 'sometimes|string|max:255',
            'nacionalidade'   => 'nullable|string|max:100',
            'data_nascimento' => 'nullable|date',
            'biografia'       => 'nullable|string',
        ]);

        $autor->update($validated);
        return response()->json($autor);
    }

    public function destroy(string $id)
    {
        $autor = Autor::findOrFail($id);
        $autor->delete();
        return response()->json(['message' => 'Autor eliminado com sucesso']);
    }
}
