<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoTransporte extends Model
{
    use HasFactory;

    protected $table = 'pedido_transportes'; // o el nombre real de tu tabla

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'origen',
        'destino',
    ];
}
