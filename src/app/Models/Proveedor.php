<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{

    protected $table = 'proveedors';
    protected $fillable = [
        'name',
        'lastName',
        'phone',
        'email',
        'businessName',
        // 'address',
        'category',
        // 'availability',
        // 'certification',
        'description',
        // 'evidence',
        'status',
    ];
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(products::class, 'provider_id', 'id');
    }
}
