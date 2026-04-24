<?php

namespace Database\Seeders;

use App\Models\Livro;
use Illuminate\Database\Seeder;

class LivroSeeder extends Seeder
{
    public function run(): void
    {
        $livros = [
            ['titulo' => 'Memorial do Convento', 'isbn' => '978-972-21-0026-3', 'ano_publicacao' => 1982, 'genero' => 'Romance', 'exemplares_disponiveis' => 5, 'autor_id' => 1],
            ['titulo' => 'Mensagem', 'isbn' => '978-972-21-0026-4', 'ano_publicacao' => 1934, 'genero' => 'Poesia', 'exemplares_disponiveis' => 3, 'autor_id' => 2],
            ['titulo' => 'Os Maias', 'isbn' => '978-972-21-0026-5', 'ano_publicacao' => 1888, 'genero' => 'Romance', 'exemplares_disponiveis' => 2, 'autor_id' => 3],
        ];

        foreach ($livros as $livro) {
            Livro::create($livro);
        }
    }
}