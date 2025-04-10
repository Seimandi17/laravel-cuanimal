<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{

    protected $table = 'proveedors';
    protected $fillable = [
        'name',
        'phone',
        'email',
        'businessName',
        'user_id',
        'category',
        'description',
        // 'address',
        // 'certification',
        // 'availability',
        // 'evidence',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(Products::class, 'provider_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
