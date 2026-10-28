<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get default IDs
        $defaultType = DB::table('property_types')->where('name', 'piso')->first()?->id ?? 1;
        $defaultOperation = DB::table('property_operations')->where('name', 'venta')->first()?->id ?? 1;
        $defaultStatus = DB::table('property_statuses')->where('name', 'disponible')->first()?->id ?? 1;

        // Populate type_id based on enum type values
        $typeMapping = [
            'piso' => DB::table('property_types')->where('name', 'piso')->value('id'),
            'casa' => DB::table('property_types')->where('name', 'casa')->value('id'),
            'local' => DB::table('property_types')->where('name', 'local')->value('id'),
            'oficina' => DB::table('property_types')->where('name', 'oficina')->value('id'),
            'garaje' => DB::table('property_types')->where('name', 'garaje')->value('id'),
            'terreno' => DB::table('property_types')->where('name', 'terreno')->value('id'),
            'otro' => DB::table('property_types')->where('name', 'otro')->value('id'),
        ];

        foreach ($typeMapping as $typeName => $typeId) {
            DB::table('properties')
                ->where('type', $typeName)
                ->update(['type_id' => $typeId]);
        }

        // Set default for any null values
        DB::table('properties')
            ->whereNull('type_id')
            ->update(['type_id' => $defaultType]);

        // Populate operation_id based on enum operation values
        $operationMapping = [
            'venta' => DB::table('property_operations')->where('name', 'venta')->value('id'),
            'alquiler' => DB::table('property_operations')->where('name', 'alquiler')->value('id'),
        ];

        foreach ($operationMapping as $operationName => $operationId) {
            DB::table('properties')
                ->where('operation', $operationName)
                ->update(['operation_id' => $operationId]);
        }

        // Set default for any null values
        DB::table('properties')
            ->whereNull('operation_id')
            ->update(['operation_id' => $defaultOperation]);

        // Populate status_id based on enum status values
        $statusMapping = [
            'disponible' => DB::table('property_statuses')->where('name', 'disponible')->value('id'),
            'reservado' => DB::table('property_statuses')->where('name', 'reservado')->value('id'),
            'vendido' => DB::table('property_statuses')->where('name', 'vendido')->value('id'),
            'alquilado' => DB::table('property_statuses')->where('name', 'alquilado')->value('id'),
        ];

        foreach ($statusMapping as $statusName => $statusId) {
            DB::table('properties')
                ->where('status', $statusName)
                ->update(['status_id' => $statusId]);
        }

        // Set default for any null values
        DB::table('properties')
            ->whereNull('status_id')
            ->update(['status_id' => $defaultStatus]);
    }

    public function down(): void
    {
        // Reset all foreign keys to null on rollback
        DB::table('properties')->update([
            'type_id' => null,
            'operation_id' => null,
            'status_id' => null,
        ]);
    }
};
