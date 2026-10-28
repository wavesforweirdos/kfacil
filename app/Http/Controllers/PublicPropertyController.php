<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Page;

class PublicPropertyController extends Controller
{
    public function index()
    {
        $propertiesPage = Page::query()->where('type', 'propiedades')->where('published', true)->first();

        if ($propertiesPage) {
            return redirect()->route('pages.show', $propertiesPage->slug);
        }

        return redirect('/');
    }

    public function show(Property $property, ?string $slug = null)
    {
        abort_unless(
            auth()->check() || Property::publiclyVisible()->whereKey($property->getKey())->exists(),
            404,
        );

        // /inmuebles/12 y /inmuebles/12/slug-viejo llevan a la URL canónica (301)
        if ($slug !== $property->slug()) {
            return redirect()->to($property->publicUrl(), 301);
        }

        $property->load(['propertyType', 'propertyOperation', 'propertyStatus', 'images']);

        return view('properties.show-public', compact('property'));
    }
}
