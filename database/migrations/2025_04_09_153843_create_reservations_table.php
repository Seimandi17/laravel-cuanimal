<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Cliente
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Servicio
            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->string('telefono_cliente')->nullable();
            $table->integer('cantidad')->default(1); // Número de mascotas o personas
            $table->text('mensaje')->nullable(); // Notas del cliente
            $table->string('direccion')->nullable();
            $table->date('fecha'); // Fecha de la reserva
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'completado'])->default('pendiente');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
    
};
