<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'province',
        'pet',
        'price',
        'contact',
        'city',
        'address',
        'provider_id',
        'coverImg',
        'extraImg',
        'codigo_postal',
        'facebook',
        'instagram',
        'x',
        'linkedin',
        'reserva_link',
        'reservar_mesa_link',
        'pedidos_domicilio_link'
    ];

    public function getCoverImgAttribute($value)
    {
        if (str_starts_with($value, '/storage/')) {
            return $value;
        }
        return $value ? Storage::url($value) : null;
    }

    public function getExtraImgAttribute($value)
    {
        if (str_starts_with($value, '/storage/')) {
            return $value;
        }
        return $value ? Storage::url($value) : null;
    }

    public function provider()
    {
        return $this->belongsTo(\App\Models\Proveedor::class, 'provider_id');
    }
}
