<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class products extends Model
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
    'coverImg',
    'extraImg'
    ];
    public function getCoverImgAttribute($value)
    {
        // Si la ruta ya tiene /storage/, devolverla tal cual
        if (str_starts_with($value, '/storage/')) {
            return $value;
        }
        // Si no, agregar el prefijo /storage/
        return $value ? Storage::url($value) : null;
    }

    // Accessor para extraImg
    public function getExtraImgAttribute($value)
    {
        // Si la ruta ya tiene /storage/, devolverla tal cual
        if (str_starts_with($value, '/storage/')) {
            return $value;
        }
        // Si no, agregar el prefijo /storage/
        return $value ? Storage::url($value) : null;
    }
    public function provider()
    {
        return $this->belongsTo(Proveedor::class, 'provider_id', 'id');
    }
}
