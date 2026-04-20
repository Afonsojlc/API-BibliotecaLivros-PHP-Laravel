<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'nacionalidade', 'data_nascimento', 'biografia'];

    // Um autor tem muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class);
    }
}