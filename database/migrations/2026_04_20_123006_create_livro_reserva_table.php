<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_livros_table.php
    // database/migrations/xxxx_create_livro_reserva_table.php
    public function up(): void
    {
        Schema::create('livro_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->foreignId('reserva_id')->constrained()->onDelete('cascade');
            $table->integer('quantidade')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livro_reserva');
    }
};
