<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoTransporte extends Model
{
    use HasFactory;

    protected $table = 'pedido_transportes';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'origen',
        'destino',
        'recogida',
        'entrega',
        'fecha',
        'adultos',
        'ninos',
        'mascotas',
    ];
}
