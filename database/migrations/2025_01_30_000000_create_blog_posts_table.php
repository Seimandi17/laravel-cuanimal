<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumen')->nullable();
            $table->longText('contenido');
            $table->string('categoria');
            $table->string('imagen')->nullable();
            $table->integer('vistas')->default(0);
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('autor_id')->nullable();
            $table->dateTime('fecha_publicacion')->nullable();
            $table->timestamps();

            $table->foreign('autor_id')->references('id')->on('users')->onDelete('set null');
            $table->index('categoria');
            $table->index('activo');
            $table->index('fecha_publicacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};

