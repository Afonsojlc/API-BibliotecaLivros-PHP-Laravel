<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Livro;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // GET /api/reservas — Admin retrieves all library reservations (Paginated)
    public function index()
    {
        $reservas = Reserva::with(['user', 'livros'])->paginate(15);
        return response()->json($reservas);
    }

    // GET /api/reservas/minhas — Reader retrieves their own reservations
    public function minhas(Request $request)
    {
        $reservas = $request->user()->reservas()->with('livros')->get();
        return response()->json($reservas);
    }

    // POST /api/reservas — Reader requests a book reservation with quantities
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_reserva'            => 'required|date|after_or_equal:today',
            'data_devolucao'          => 'required|date|after:data_reserva',
            'livros'                  => 'required|array|min:1',
            'livros.*.id'             => 'required|exists:livros,id',
            'livros.*.quantidade'     => 'required|integer|min:1',
        ]);

        $reserva = Reserva::create([
            'user_id'        => $request->user()->id,
            'data_reserva'   => $validated['data_reserva'],
            'data_devolucao' => $validated['data_devolucao'],
            'estado'         => 'pendente',
        ]);

        // Attach books with requested quantities via pivot table (livro_reserva)
        foreach ($validated['livros'] as $livro) {
            $reserva->livros()->attach($livro['id'], ['quantidade' => $livro['quantidade']]);
        }

        return response()->json($reserva->load('livros'), 201);
    }

    // PATCH /api/reservas/{id} — Admin updates reservation status (pendente, ativa, devolvida)
    public function updateEstado(Request $request, string $id)
    {
        $reserva = Reserva::findOrFail($id);
        $request->validate(['estado' => 'required|in:pendente,ativa,devolvida']);
        $reserva->update(['estado' => $request->estado]);
        return response()->json($reserva);
    }
}
