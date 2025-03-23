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
        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lastName');
            $table->string('phone');
            $table->string('email');
            // $table->string('address');
            $table->string('category');
            $table->string('businessName');
            // $table->string('availability');
            // $table->string('certification')->nullable();
            $table->string('description');
            // $table->string('evidence');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provedores');
    }
};
