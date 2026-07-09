<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="h5 mb-1 fw-semibold text-dark">Editor de navbar global</h2>
                <p class="small text-muted mb-0">Configuracion unica para todas las paginas publicas</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary btn-sm px-3">Volver a contenidos</a>
            </div>
        </div>
    </x-slot>

    <div class="py-2 bg-light min-vh-100 accent-blue page-edit-shell">
        <div class="container-fluid px-4">
            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small mb-3" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $navbarLinks = data_get($navbarSettings ?? [], 'links', []);
                if (!is_array($navbarLinks) || count($navbarLinks) === 0) {
                    $navbarLinks = [
                        ['label' => 'Inicio', 'url' => '/inicio'],
                        ['label' => 'Servicios', 'url' => '/servicios'],
                        ['label' => 'Propiedades', 'url' => '/inmuebles'],
                        ['label' => 'Contacto', 'url' => '/contacto'],
                    ];
                }
            @endphp

            <div class="card border-0 shadow-sm rounded-4 page-meta-card mb-3" x-data="{ links: @js(old('links_label') ? collect(old('links_label'))->values()->map(fn($label, $idx) => ['label' => $label, 'url' => old('links_url.' . $idx)])->toArray() : $navbarLinks) }">
                <div class="card-header page-meta-head border-0 py-3 px-3 px-lg-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h3 class="h6 fw-semibold mb-1 text-white">Navbar global</h3>
                        <p class="small mb-0 text-white-50">Se aplica automaticamente en todo el sitio publico</p>
                    </div>
                    <span class="badge rounded-pill bg-white text-primary-emphasis text-uppercase px-3">Global</span>
                </div>

                <div class="card-body p-3 p-lg-4">
                    <form action="{{ route('pages.navbar.update') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-12 col-md-4">
                            <label class="form-label">Layout</label>
                            <select name="layout" class="form-select form-select-sm">
                                <option value="horizontal" {{ data_get($navbarSettings, 'layout', 'horizontal') === 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                                <option value="lateral" {{ data_get($navbarSettings, 'layout') === 'lateral' ? 'selected' : '' }}>Lateral</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label">Marca</label>
                            <input type="text" name="brand" value="{{ old('brand', data_get($navbarSettings, 'brand', 'KFACIL')) }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" accept="image/*" class="form-control form-control-sm">
                            @if(data_get($navbarSettings, 'logo'))
                                <img src="{{ data_get($navbarSettings, 'logo') }}" alt="Logo actual" class="img-fluid rounded border mt-2" style="max-height: 56px; object-fit: contain;">
                            @endif
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label">Color fondo</label>
                            <input type="color" name="background_color" value="{{ old('background_color', data_get($navbarSettings, 'background_color', '#0f172a')) }}" class="form-control form-control-color form-control-sm">
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label">Color texto</label>
                            <input type="color" name="text_color" value="{{ old('text_color', data_get($navbarSettings, 'text_color', '#ffffff')) }}" class="form-control form-control-color form-control-sm">
                        </div>

                        <div class="col-12 col-md-3 d-flex align-items-end">
                            <div class="form-check mb-1">
                                <input type="checkbox" name="show_cta" value="1" class="form-check-input" id="show_cta" {{ old('show_cta', data_get($navbarSettings, 'show_cta')) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="show_cta">Mostrar CTA</label>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label">Texto CTA</label>
                            <input type="text" name="cta_text" value="{{ old('cta_text', data_get($navbarSettings, 'cta_text', 'Contactar')) }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-12">
                            <label class="form-label">URL CTA</label>
                            <input type="text" name="cta_url" value="{{ old('cta_url', data_get($navbarSettings, 'cta_url', '/contacto')) }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Links del navbar</label>
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary btn-sm px-2 py-1"
                                    @click="links.push({ label: '', url: '' })"
                                >
                                    <i class="fas fa-plus me-1"></i>Añadir enlace
                                </button>
                            </div>

                            <div class="vstack gap-2">
                                <template x-for="(item, index) in links" :key="index">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 col-md-4">
                                            <input type="text" class="form-control form-control-sm" placeholder="Etiqueta" x-model="item.label" :name="`links_label[${index}]`">
                                        </div>
                                        <div class="col-12 col-md-7">
                                            <input type="text" class="form-control form-control-sm" placeholder="/ruta o https://..." x-model="item.url" :name="`links_url[${index}]`">
                                        </div>
                                        <div class="col-12 col-md-1 d-grid">
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Eliminar enlace"
                                                @click="links.splice(index, 1)"
                                                x-bind:disabled="links.length <= 1"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="col-12 pt-1">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="fas fa-compass me-1"></i>Guardar navbar global
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-edit-shell {
            background: radial-gradient(circle at 8% 12%, #eef6ff 0%, #f4f7fb 42%, #f5f4f8 100%);
        }

        .page-meta-card {
            overflow: hidden;
        }

        .page-meta-head {
            background: linear-gradient(120deg, #2032a6 0%, #2a55cb 55%, #3a7be6 100%);
        }
    </style>
</x-admin-layout>
