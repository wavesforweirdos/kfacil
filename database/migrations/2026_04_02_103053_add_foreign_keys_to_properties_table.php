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
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->after('operation')->constrained('property_types');
            $table->foreignId('operation_id')->nullable()->after('type_id')->constrained('property_operations');
            $table->foreignId('status_id')->nullable()->after('operation_id')->constrained('property_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['type_id']);
            $table->dropForeignKeyIfExists(['operation_id']);
            $table->dropForeignKeyIfExists(['status_id']);
            $table->dropColumn(['type_id', 'operation_id', 'status_id']);
        });
    }
};
