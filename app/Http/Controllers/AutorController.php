<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    // GET /api/autores — List authors with their total book count (Paginated)
    public function index()
    {
        $autores = Autor::withCount('livros')->paginate(10);
        return response()->json($autores);
    }

    // POST /api/autores — Admin creates author record
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

    // GET /api/autores/{id} — Retrieve author details including their books
    public function show(string $id)
    {
        $autor = Autor::with('livros')->findOrFail($id);
        return response()->json($autor);
    }

    // PUT /api/autores/{id} — Admin updates author details
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

    // DELETE /api/autores/{id} — Admin deletes author from catalog
    public function destroy(string $id)
    {
        $autor = Autor::findOrFail($id);
        $autor->delete();
        return response()->json(['message' => 'Author removed from catalog successfully.']);
    }
}
