<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    // GET /api/livros — List books with author details, optional filters and pagination
    public function index(Request $request)
    {
        $query = Livro::with('autor');

        // Optional filter by literary genre
        if ($request->has('genero')) {
            $query->where('genero', $request->genero);
        }

        // Optional filter for available stock
        if ($request->has('disponivel')) {
            $query->where('exemplares_disponiveis', '>', 0);
        }

        return response()->json($query->paginate(10));
    }

    // POST /api/livros — Admin adds a new book to catalog
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'                  => 'required|string|max:255',
            'isbn'                    => 'required|string|unique:livros',
            'ano_publicacao'          => 'required|integer|min:1000|max:2100',
            'genero'                  => 'required|string|max:100',
            'exemplares_disponiveis'  => 'required|integer|min:0',
            'autor_id'                => 'required|exists:autors,id',
        ]);

        $livro = Livro::create($validated);
        return response()->json($livro->load('autor'), 201);
    }

    // GET /api/livros/{id} — Retrieve book details with author
    public function show(string $id)
    {
        $livro = Livro::with('autor')->findOrFail($id);
        return response()->json($livro);
    }

    // PUT /api/livros/{id} — Admin updates book details
    public function update(Request $request, string $id)
    {
        $livro = Livro::findOrFail($id);
        $validated = $request->validate([
            'titulo'                  => 'sometimes|string|max:255',
            'isbn'                    => 'sometimes|string|unique:livros,isbn,'.$id,
            'ano_publicacao'          => 'sometimes|integer|min:1000|max:2100',
            'genero'                  => 'sometimes|string|max:100',
            'exemplares_disponiveis'  => 'sometimes|integer|min:0',
            'autor_id'                => 'sometimes|exists:autors,id',
        ]);

        $livro->update($validated);
        return response()->json($livro->load('autor'));
    }

    // DELETE /api/livros/{id} — Admin deletes book from catalog
    public function destroy(string $id)
    {
        $livro = Livro::findOrFail($id);
        $livro->delete();
        return response()->json(['message' => 'Book removed from catalog successfully.']);
    }
}
