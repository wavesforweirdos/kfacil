<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyStatus extends Model
{
    protected $fillable = ['name', 'label', 'sort_order', 'is_active'];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'status_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getTranslatableLabel(): string
    {
        $transKey = "admin_properties.status_{$this->name}";
        if (trans()->has($transKey)) {
            return trans($transKey);
        }

        return $this->label;
    }

    public function isClosed(): bool
    {
        return in_array($this->name, ['vendido', 'alquilado']);
    }
}
