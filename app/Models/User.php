<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- 1. Importar o Sanctum aqui

// <-- 2. Adicionar o 'role' aqui no #[Fillable]
#[Fillable(['name', 'email', 'password', 'role'])] 
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // <-- 3. Adicionar o HasApiTokens aqui no início
    use HasApiTokens, HasFactory, Notifiable; 

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // <-- 4. Os nossos métodos da Biblioteca em baixo
    
    // Um utilizador tem muitas reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Método auxiliar para verificar o role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}