<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'data_reserva', 'data_devolucao', 'estado'];

    // Uma reserva pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Uma reserva inclui vários livros
    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'livro_reserva')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
}