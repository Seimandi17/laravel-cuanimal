<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        'name',
        'lastName',
        'phone',
        'email',
        'address',
        'services',
        'availability',
        'certification',
        'description',
        'evidence',
        'status',
    ];
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
