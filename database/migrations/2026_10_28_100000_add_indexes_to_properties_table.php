<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices para lo que más se filtra y ordena en el listado y en la web pública.
     * (properties.reference ya es unique y las claves foráneas ya tienen índice propio.)
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->index('price');
            $table->index('city');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['price']);
            $table->dropIndex(['city']);
            $table->dropIndex(['created_at']);
        });
    }
};
