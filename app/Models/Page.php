<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'type',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'editable_by_agents',
        'published',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'editable_by_agents' => 'boolean',
        'published' => 'boolean',
    ];

    /**
     * Generate slug from title if not provided
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
            if (! isset($page->created_by)) {
                $page->created_by = auth()->id();
            }
        });

        static::updating(function ($page) {
            $page->updated_by = auth()->id();
        });
    }

    // Relationships
    public function blocks()
    {
        return $this->hasMany(PageBlock::class)->orderBy('order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEditableByAgents($query)
    {
        return $query->where('editable_by_agents', true);
    }

    // Methods
    public function canBeEditedBy($user)
    {
        if ($user->hasPermission('edit_all_pages')) {
            return true;
        }

        if ($user->hasPermission('edit_own_pages') && $this->created_by === $user->id) {
            return true;
        }

        if ($this->editable_by_agents && $user->hasPermission('edit_agent_pages')) {
            return true;
        }

        return false;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
