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

    public function show(Property $property)
    {
        $property->load(['propertyType', 'propertyOperation', 'propertyStatus', 'images']);

        return view('properties.show-public', compact('property'));
    }
}
