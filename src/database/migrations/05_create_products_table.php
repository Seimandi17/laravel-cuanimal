<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->unsignedBigInteger('category_id');
            // $table->foreign('category_id')->references('id')->on('categories');
            $table->text('category');
            $table->text('description');
            $table->float('price');
            $table->string('contact')->nullable();
            $table->string('province');
            $table->string('pet')->default('ambos');
            $table->string('city');
            $table->string('address');
            $table->string('status')->default('activo');
            $table->unsignedBigInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('proveedors');
            $table->string('coverImg');
            $table->string('extraImg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
