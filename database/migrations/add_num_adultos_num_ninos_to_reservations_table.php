<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedInteger('num_adultos')->default(1)->after('cantidad');
            $table->unsignedInteger('num_niños')->default(0)->after('num_adultos');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('num_adultos');
            $table->dropColumn('num_niños');
        });
    }
};
