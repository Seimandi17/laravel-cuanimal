<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'categoria',
        'imagen_url',
        'vistas',
        'es_destacado',
        'es_publicado',
        'activo',
    ];

    protected $casts = [
        'es_destacado' => 'boolean',
        'es_publicado' => 'boolean',
        'activo' => 'boolean',
        'vistas' => 'integer',
    ];

    /**
     * Incrementar contador de vistas
     */
    public function incrementarVistas()
    {
        $this->increment('vistas');
    }
}

