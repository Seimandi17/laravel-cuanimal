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
        Schema::table('products', function (Blueprint $table) {
            $table->string('codigo_postal')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('x')->nullable();
            $table->string('linkedin')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['codigo_postal', 'facebook', 'instagram', 'x', 'linkedin']);
        });
    }
};
