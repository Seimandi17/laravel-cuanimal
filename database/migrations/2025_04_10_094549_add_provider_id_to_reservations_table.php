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
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->after('user_id');
    
            // Si la tabla proveedores existe:
            $table->foreign('provider_id')->references('id')->on('proveedors')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('provider_id');
        });
    }
    
};
