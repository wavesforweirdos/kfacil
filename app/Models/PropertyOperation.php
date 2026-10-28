<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyOperation extends Model
{
    protected $fillable = ['name', 'label', 'sort_order', 'is_active'];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'operation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getTranslatableLabel(): string
    {
        $transKey = "admin_properties.operation_{$this->name}";
        if (trans()->has($transKey)) {
            return trans($transKey);
        }

        return $this->label;
    }
}
