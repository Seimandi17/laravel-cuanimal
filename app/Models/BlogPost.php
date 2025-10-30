<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'categoria',
        'imagen',
        'vistas',
        'destacado',
        'activo',
        'autor_id',
        'fecha_publicacion',
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'vistas' => 'integer',
    ];

    protected $dates = [
        'fecha_publicacion',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación con el usuario autor
     */
    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    /**
     * Generar slug automáticamente desde el título
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->titulo);
                
                // Asegurar que el slug sea único
                $count = static::where('slug', 'like', $post->slug . '%')->count();
                if ($count > 0) {
                    $post->slug = $post->slug . '-' . ($count + 1);
                }
            }

            // Si no hay fecha de publicación, usar la fecha actual
            if (empty($post->fecha_publicacion)) {
                $post->fecha_publicacion = now();
            }
        });

        static::updating(function ($post) {
            // Si el título cambió, actualizar el slug
            if ($post->isDirty('titulo') && empty($post->slug)) {
                $post->slug = Str::slug($post->titulo);
            }
        });
    }

    /**
     * Scope para obtener solo posts activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener posts por categoría
     */
    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para posts destacados
     */
    public function scopeDestacado($query)
    {
        return $query->where('destacado', true);
    }

    /**
     * Scope para ordenar por fecha de publicación
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_publicacion', 'desc');
    }

    /**
     * Incrementar contador de vistas
     */
    public function incrementarVistas()
    {
        $this->increment('vistas');
    }
}

