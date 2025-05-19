<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recorrido extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'resumen',
        'provincia',
        'contenido',
        'imagen',
        'video',
    ];
}
