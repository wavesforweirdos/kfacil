<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    protected $fillable = [
        'page_id',
        'component',
        'order',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    // Get component config based on type
    public static function getComponentSchema($component)
    {
        $schemas = [
            'hero' => [
                'title' => 'string',
                'subtitle' => 'string',
                'image' => 'image',
                'cta_text' => 'string',
                'cta_url' => 'string',
                'height' => 'select:sm,md,lg,xl',
            ],
            'gallery' => [
                'title' => 'string',
                'images' => 'array',
                'columns' => 'select:2,3,4',
            ],
            'testimonials' => [
                'title' => 'string',
                'testimonials' => 'array',
            ],
            'cta' => [
                'title' => 'string',
                'description' => 'text',
                'button_text' => 'string',
                'button_url' => 'string',
                'background_color' => 'color',
            ],
            'properties_search' => [
                'title' => 'string',
                'view_type' => 'select:list,cards,grid',
                'items_per_page' => 'number',
            ],
            'properties_featured' => [
                'title' => 'string',
                'limit' => 'number',
                'columns' => 'select:2,3,4',
            ],
            'services' => [
                'title' => 'string',
                'services' => 'array',
                'columns' => 'select:2,3,4',
            ],
            'contact_form' => [
                'title' => 'string',
                'description' => 'text',
                'show_map' => 'boolean',
            ],
        ];

        return $schemas[$component] ?? [];
    }
}
