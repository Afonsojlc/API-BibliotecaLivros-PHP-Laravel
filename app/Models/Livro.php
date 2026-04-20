<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'isbn', 'ano_publicacao', 'genero',
                           'exemplares_disponiveis', 'autor_id'];

    // Um livro pertence a um autor
    public function autor()
    {
        return $this->belongsTo(Autor::class);
    }

    // Um livro pode estar em muitas reservas
    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'livro_reserva')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
}