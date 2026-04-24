<?php

namespace Database\Seeders;

use App\Models\Autor;
use Illuminate\Database\Seeder;

class AutorSeeder extends Seeder
{
    public function run(): void
    {
        $autores = [
            ['nome' => 'José Saramago',   'nacionalidade' => 'Portuguesa', 'data_nascimento' => '1922-11-16'],
            ['nome' => 'Fernando Pessoa', 'nacionalidade' => 'Portuguesa', 'data_nascimento' => '1888-06-13'],
            ['nome' => 'Eça de Queirós',  'nacionalidade' => 'Portuguesa', 'data_nascimento' => '1845-11-25'],
        ];

        foreach ($autores as $autor) {
            Autor::create($autor);
        }
    }
}