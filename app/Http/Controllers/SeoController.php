<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Property;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /** sitemap.xml: las páginas publicadas y los inmuebles visibles en la web pública. */
    public function sitemap(): Response
    {
        $urls = [];

        foreach (Page::where('published', true)->orderBy('id')->get() as $page) {
            $urls[] = [
                'loc' => $page->type === 'home' ? url('/') : url($page->slug),
                'lastmod' => $page->updated_at?->toAtomString(),
            ];
        }

        foreach (Property::publiclyVisible()->orderBy('id')->get() as $property) {
            $urls[] = [
                'loc' => $property->publicUrl(),
                'lastmod' => $property->updated_at?->toAtomString(),
            ];
        }

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /** robots.txt: el CRM no se indexa y se anuncia el sitemap. */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /agente',
            'Disallow: /login',
            'Disallow: /profile',
            'Disallow: /pages',
            'Disallow: /messages',
            'Disallow: /property-types',
            'Disallow: /property-operations',
            'Disallow: /property-statuses',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
