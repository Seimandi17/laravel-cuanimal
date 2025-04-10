<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_id',
        'product_id',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'cantidad',
        'mensaje',
        'direccion',
        'fecha',
        'estado',
    ];

    // 🔁 Relación con el usuario (cliente)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔁 Relación con el proveedor
    public function provider()
    {
        return $this->belongsTo(Proveedor::class, 'provider_id');
    }

    // 🔁 Relación con el producto (servicio reservado)
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
