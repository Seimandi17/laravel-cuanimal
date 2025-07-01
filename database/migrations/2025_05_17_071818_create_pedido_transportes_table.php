<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoTransportesTable extends Migration
{
    public function up()
    {
        Schema::create('pedido_transportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono');
            $table->string('origen')->nullable();
            $table->string('destino')->nullable();
            $table->string('recogida'); // dirección de recogida
            $table->string('entrega'); // dirección de entrega
            $table->date('fecha'); // fecha del traslado
            $table->unsignedInteger('adultos')->default(0);
            $table->unsignedInteger('ninos')->default(0);
            $table->unsignedInteger('mascotas')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedido_transportes');
    }
}
