<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('codigo_postal')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('x')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('reserva_link')->nullable();              // NUEVO
            $table->string('reservar_mesa_link')->nullable();        // NUEVO
            $table->string('pedidos_domicilio_link')->nullable();    // NUEVO
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_postal',
                'facebook',
                'instagram',
                'x',
                'linkedin',
                'reserva_link',             // NUEVO
                'reservar_mesa_link',       // NUEVO
                'pedidos_domicilio_link',   // NUEVO
            ]);
        });
    }
};

