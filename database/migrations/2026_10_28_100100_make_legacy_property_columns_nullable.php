<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `type`, `operation` y `status` eran enums obligatorios de antes de existir los
     * catálogos (property_types/operations/statuses). El formulario solo envía los *_id, así
     * que crear un inmueble fallaba con "Field 'type' doesn't have a default value".
     * Se dejan como texto opcional: solo los lee la ficha como respaldo de datos antiguos.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->change();
            $table->string('operation', 50)->nullable()->change();
            $table->string('status', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        // No se vuelve a los enums obligatorios: dejaría de poder crearse cualquier inmueble nuevo.
    }
};
