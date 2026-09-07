<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Livro;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookLibraryApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration and token generation.
     */
    public function test_user_can_register_as_reader_and_receive_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name'     => 'Alice Reader',
            'email'    => 'alice@example.com',
            'password' => 'secret123',
            'role'     => 'leitor',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'name', 'email', 'role']]);

        $this->assertDatabaseHas('users', [
            'email' => 'alice@example.com',
            'role'  => 'leitor',
        ]);
    }

    /**
     * Test authenticated catalog browsing for books.
     */
    public function test_authenticated_user_can_browse_books_catalog(): void
    {
        $user = User::factory()->create(['role' => 'leitor']);
        $autor = Autor::create([
            'nome' => 'Fernando Pessoa',
            'nacionalidade' => 'Portuguesa'
        ]);

        Livro::create([
            'titulo' => 'Mensagem',
            'isbn' => '978-972-21-0026-4',
            'ano_publicacao' => 1934,
            'genero' => 'Poesia',
            'exemplares_disponiveis' => 3,
            'autor_id' => $autor->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/livros?genero=Poesia');

        $response->assertStatus(200)
            ->assertJsonFragment(['titulo' => 'Mensagem']);
    }

    /**
     * Test reader can book a reservation with books.
     */
    public function test_reader_can_reserve_books(): void
    {
        $reader = User::factory()->create(['role' => 'leitor']);
        $autor = Autor::create(['nome' => 'José Saramago']);
        $livro = Livro::create([
            'titulo' => 'Memorial do Convento',
            'isbn' => '978-972-21-0026-3',
            'ano_publicacao' => 1982,
            'genero' => 'Romance',
            'exemplares_disponiveis' => 5,
            'autor_id' => $autor->id,
        ]);

        $response = $this->actingAs($reader, 'sanctum')->postJson('/api/reservas', [
            'data_reserva' => now()->toDateString(),
            'data_devolucao' => now()->addDays(14)->toDateString(),
            'livros' => [
                ['id' => $livro->id, 'quantidade' => 1]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['estado' => 'pendente']);

        $this->assertDatabaseHas('reservas', [
            'user_id' => $reader->id,
            'estado'  => 'pendente',
        ]);
    }

    /**
     * Test role protection preventing readers from managing library catalog.
     */
    public function test_reader_cannot_create_book_or_author(): void
    {
        $reader = User::factory()->create(['role' => 'leitor']);

        $response = $this->actingAs($reader, 'sanctum')->postJson('/api/autores', [
            'nome' => 'Unauthorized Author',
        ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Access denied: Insufficient role permissions.']);
    }

    /**
     * Test admin updating reservation status.
     */
    public function test_admin_can_update_reservation_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reader = User::factory()->create(['role' => 'leitor']);

        $reserva = Reserva::create([
            'user_id' => $reader->id,
            'data_reserva' => now()->toDateString(),
            'data_devolucao' => now()->addDays(7)->toDateString(),
            'estado' => 'pendente',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/reservas/{$reserva->id}", [
            'estado' => 'ativa',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['estado' => 'ativa']);

        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'estado' => 'ativa',
        ]);
    }
}
