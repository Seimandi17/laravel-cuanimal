<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // Agregar imagen_url si no existe
            if (!Schema::hasColumn('blog_posts', 'imagen_url')) {
                $table->string('imagen_url')->nullable()->after('categoria');
            }

            // Agregar es_destacado si no existe
            if (!Schema::hasColumn('blog_posts', 'es_destacado')) {
                $table->boolean('es_destacado')->default(false)->after('vistas');
            }

            // Agregar es_publicado si no existe
            if (!Schema::hasColumn('blog_posts', 'es_publicado')) {
                $table->boolean('es_publicado')->default(false)->after('es_destacado');
            }
        });

        // Copiar datos de columnas antiguas a nuevas si existen
        if (Schema::hasColumn('blog_posts', 'imagen') && Schema::hasColumn('blog_posts', 'imagen_url')) {
            DB::statement('UPDATE blog_posts SET imagen_url = imagen WHERE imagen_url IS NULL');
        }

        if (Schema::hasColumn('blog_posts', 'destacado') && Schema::hasColumn('blog_posts', 'es_destacado')) {
            DB::statement('UPDATE blog_posts SET es_destacado = destacado');
        }

        // Eliminar columnas antiguas si existen (opcional, puedes comentar estas líneas si prefieres mantenerlas)
        Schema::table('blog_posts', function (Blueprint $table) {
            if (Schema::hasColumn('blog_posts', 'imagen') && Schema::hasColumn('blog_posts', 'imagen_url')) {
                $table->dropColumn('imagen');
            }
            
            if (Schema::hasColumn('blog_posts', 'destacado') && Schema::hasColumn('blog_posts', 'es_destacado')) {
                $table->dropColumn('destacado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // Revertir cambios
            if (Schema::hasColumn('blog_posts', 'imagen_url')) {
                $table->renameColumn('imagen_url', 'imagen');
            }
            
            if (Schema::hasColumn('blog_posts', 'es_destacado')) {
                $table->renameColumn('es_destacado', 'destacado');
            }
            
            if (Schema::hasColumn('blog_posts', 'es_publicado')) {
                $table->dropColumn('es_publicado');
            }
        });
    }
};
