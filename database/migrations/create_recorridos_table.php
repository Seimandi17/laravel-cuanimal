<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recorridos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('resumen');
            $table->string('provincia');
            $table->text('contenido');
            $table->string('imagen')->nullable();
            $table->string('video')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recorridos');
    }
};
