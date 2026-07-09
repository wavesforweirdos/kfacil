<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference',
        'title',
        'description',
        'type',
        'operation',
        'status',
        'type_id',
        'operation_id',
        'status_id',
        'price',
        'surface',
        'rooms',
        'bathrooms',
        'address',
        'city',
        'locality',
        'province',
        'country',
        'latitude',
        'longitude',
        'main_image',
    ];

    protected function casts(): array
    {
        return [
            'price'     => 'decimal:2',
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /* ── Reference generation ── */

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Property $property) {
            if (empty($property->reference)) {
                $property->reference = static::generateReference();
            }
        });
    }

    /**
     * Generate a unique reference.
     * - No prefix  → XXXXX  (5 random digits)
     * - With prefix → PREFIX-XXXXX
     */
    public static function generateReference(?string $prefix = null): string
    {
        do {
            $digits = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $ref    = $prefix ? strtoupper(trim($prefix)) . '-' . $digits : $digits;
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    /* ── Relaciones ── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'type_id');
    }

    public function propertyOperation(): BelongsTo
    {
        return $this->belongsTo(PropertyOperation::class, 'operation_id');
    }

    public function propertyStatus(): BelongsTo
    {
        return $this->belongsTo(PropertyStatus::class, 'status_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('order');
    }

    public function secondaryImages(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->where('type', 'secondary')->orderBy('order');
    }

    public function floorPlans(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->where('type', 'floor_plan')->orderBy('order');
    }

    /* ── Helpers ── */

    public function mainImageUrl(): ?string
    {
        if (!$this->main_image) {
            return null;
        }
        if (str_starts_with($this->main_image, 'http')) {
            return $this->main_image;
        }
        return asset('storage/' . $this->main_image);
    }

    public function formattedPrice(): string
    {
        return number_format($this->price, 0, ',', '.') . ' €';
    }

    public function statusBadgeClass(): string
    {
        $status = $this->propertyStatus?->name ?? $this->status;
        return match ($status) {
            'disponible' => 'badge-status-available',
            'reservado'  => 'badge-status-reserved',
            'vendido',
            'alquilado'  => 'badge-status-closed',
            default      => 'bg-light text-muted',
        };
    }

    public function operationBadgeClass(): string
    {
        $operation = $this->propertyOperation?->name ?? $this->operation;
        return match ($operation) {
            'venta'    => 'badge-accent',
            'alquiler' => 'badge-accent-soft',
            default    => '',
        };
    }
}
