<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::paginate(15);

        return view('pages.index', compact('pages'));
    }

    public function create()
    {
        $this->authorize('create', Page::class);

        return view('pages.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Page::class);

        $validated = $request->validate([
            'slug' => 'required|string|unique:pages,slug',
            'title' => 'required|string',
            'type' => 'required|in:home,servicios,contacto,propiedades',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $page = Page::create($validated);

        return redirect()->route('pages.edit', $page)->with('success', 'Página creada correctamente');
    }

    public function edit(Page $page)
    {
        $this->authorize('update', $page);

        return view('pages.edit', compact('page'));
    }

    public function editNavbar()
    {
        $this->authorize('viewAny', Page::class);

        $navbarSettings = SiteSetting::getValue('navbar', [
            'layout' => 'horizontal',
            'brand' => 'KFACIL',
            'logo' => null,
            'background_color' => '#0f172a',
            'text_color' => '#ffffff',
            'show_cta' => false,
            'cta_text' => 'Contactar',
            'cta_url' => '/contacto',
            'links' => [
                ['label' => 'Inicio', 'url' => '/inicio'],
                ['label' => 'Servicios', 'url' => '/servicios'],
                ['label' => 'Propiedades', 'url' => '/inmuebles'],
                ['label' => 'Contacto', 'url' => '/contacto'],
            ],
        ]);

        return view('pages.navbar-edit', compact('navbarSettings'));
    }

    public function update(Request $request, Page $page)
    {
        $this->authorize('update', $page);

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'editable_by_agents' => 'nullable|boolean',
            'published' => 'nullable|boolean',
        ]);

        $page->update($validated);

        return redirect()->route('pages.edit', $page)->with('success', 'Página actualizada correctamente');
    }

    public function publish(Page $page)
    {
        $this->authorize('update', $page);

        $page->update(['published' => true]);

        return redirect()->back()->with('success', 'Página publicada');
    }

    public function unpublish(Page $page)
    {
        $this->authorize('update', $page);

        $page->update(['published' => false]);

        return redirect()->back()->with('success', 'Página despublicada');
    }

    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);

        $page->delete();

        return redirect()->route('pages.index')->with('success', 'Página eliminada');
    }

    public function updateNavbar(Request $request)
    {
        $this->authorize('viewAny', Page::class);

        $validated = $request->validate([
            'layout' => 'required|in:horizontal,lateral',
            'brand' => 'required|string|max:80',
            'logo' => 'nullable|image|max:5120',
            'background_color' => 'required|string|max:20',
            'text_color' => 'required|string|max:20',
            'show_cta' => 'nullable|boolean',
            'cta_text' => 'nullable|string|max:80',
            'cta_url' => 'nullable|string|max:255',
            'links_label' => 'nullable|array',
            'links_label.*' => 'nullable|string|max:80',
            'links_url' => 'nullable|array',
            'links_url.*' => 'nullable|string|max:255',
        ]);

        $current = SiteSetting::getValue('navbar', []);
        $logoPath = $current['logo'] ?? null;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('site/navbar', 'public');
            $logoPath = Storage::url($path);
        }

        $links = [];
        $labels = $validated['links_label'] ?? [];
        $urls = $validated['links_url'] ?? [];

        $max = max(count($labels), count($urls));
        for ($i = 0; $i < $max; $i++) {
            $label = trim((string) ($labels[$i] ?? ''));
            $url = trim((string) ($urls[$i] ?? ''));
            if ($label !== '' && $url !== '') {
                $links[] = ['label' => $label, 'url' => $url];
            }
        }

        if (count($links) === 0) {
            $links = [
                ['label' => 'Inicio', 'url' => '/inicio'],
                ['label' => 'Servicios', 'url' => '/servicios'],
                ['label' => 'Propiedades', 'url' => '/inmuebles'],
                ['label' => 'Contacto', 'url' => '/contacto'],
            ];
        }

        SiteSetting::setValue('navbar', [
            'layout' => $validated['layout'],
            'brand' => $validated['brand'],
            'logo' => $logoPath,
            'background_color' => $validated['background_color'],
            'text_color' => $validated['text_color'],
            'show_cta' => (bool) ($validated['show_cta'] ?? false),
            'cta_text' => $validated['cta_text'] ?? 'Contactar',
            'cta_url' => $validated['cta_url'] ?? '/contacto',
            'links' => $links,
        ]);

        return redirect()->route('pages.navbar.edit')->with('success', 'Navbar global actualizado correctamente');
    }

    // Public views
    public function show(Page $page)
    {
        if (! $page->published && ! auth()->check()) {
            abort(404);
        }

        $navbarSettings = SiteSetting::getValue('navbar', [
            'layout' => 'horizontal',
            'brand' => 'KFACIL',
            'logo' => null,
            'background_color' => '#0f172a',
            'text_color' => '#ffffff',
            'show_cta' => false,
            'cta_text' => 'Contactar',
            'cta_url' => '/contacto',
            'links' => [],
        ]);

        return view('pages.show', compact('page', 'navbarSettings'));
    }
}
