<?php

namespace App\Support;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Consultas de la web pública sobre inmuebles. Los bloques de las páginas las usan en vez de
 * montar la consulta dentro de la vista, y solo devuelven inmuebles visibles (no los vendidos
 * ni los alquilados).
 */
class PublicProperties
{
    /** Destacados: los últimos inmuebles visibles. */
    public static function featured(int $limit = 6): Collection
    {
        return Property::query()
            ->publiclyVisible()
            ->latest()
            ->limit(max(1, min($limit, 24)))
            ->get();
    }

    /**
     * Buscador. Filtros admitidos: search (texto en el título), type (id del tipo),
     * min_price y max_price (numéricos; lo que no lo sea se ignora).
     *
     * @param  array<string, mixed>  $filters
     */
    public static function search(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $text = trim((string) ($filters['search'] ?? ''));
        $type = $filters['type'] ?? null;
        $min = $filters['min_price'] ?? null;
        $max = $filters['max_price'] ?? null;

        return Property::query()
            ->publiclyVisible()
            ->when($text !== '', fn ($q) => $q->where('title', 'like', '%'.addcslashes($text, '%_\\').'%'))
            ->when(is_numeric($type), fn ($q) => $q->where('type_id', (int) $type))
            ->when(is_numeric($min), fn ($q) => $q->where('price', '>=', $min))
            ->when(is_numeric($max), fn ($q) => $q->where('price', '<=', $max))
            ->latest()
            ->paginate(max(1, min($perPage, 48)))
            ->withQueryString();
    }
}
