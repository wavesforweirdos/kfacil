<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="h5 mb-1 fw-semibold text-dark">Studio de paginas</h2>
                <p class="small text-muted mb-0">Edicion visual con vista previa en directo</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis text-uppercase px-3 py-2">{{ ucfirst($page->type) }}</span>
                <a href="{{ route('pages.show', $page) }}" target="_blank" class="btn btn-outline-dark btn-sm px-3">
                    <i class="fas fa-eye me-1"></i>Ver pagina
                </a>
                <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary btn-sm px-3">Volver</a>
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

            <div class="card border-0 shadow-sm rounded-4 page-meta-card mb-3">
                <div class="card-header page-meta-head border-0 py-3 px-3 px-lg-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h3 class="h6 fw-semibold mb-1 text-white">Configuracion</h3>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill bg-white text-primary-emphasis px-3">{{ $page->slug }}</span>
                        <span class="badge rounded-pill bg-white text-primary-emphasis px-3">{{ strtoupper($page->type) }}</span>
                    </div>
                </div>

                <div class="card-body p-3 p-lg-4">
                    <form action="{{ route('pages.update', $page) }}" method="POST" class="row g-3 g-lg-4 align-items-start">
                        @csrf
                        @method('PUT')

                        <div class="col-12 col-xl-4">
                            <label class="form-label">Titulo</label>
                            <input type="text" name="title" value="{{ old('title', $page->title) }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label">Slug</label>
                            <input type="text" value="{{ $page->slug }}" disabled class="form-control form-control-sm bg-light-subtle">
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label">Tipo</label>
                            <input type="text" value="{{ ucfirst($page->type) }}" disabled class="form-control form-control-sm bg-light-subtle">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripcion</label>
                            <textarea name="description" rows="2" class="form-control form-control-sm">{{ old('description', $page->description) }}</textarea>
                        </div>

                        <div class="col-12 col-xl-6">
                            <div class="rounded-3 border p-3 bg-light-subtle h-100">
                                <p class="small text-uppercase fw-semibold text-muted mb-2">SEO</p>

                                <div class="mb-2">
                                    <label class="form-label">Meta titulo</label>
                                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="form-control form-control-sm" maxlength="60">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Meta descripcion</label>
                                    <textarea name="meta_description" rows="2" class="form-control form-control-sm" maxlength="160">{{ old('meta_description', $page->meta_description) }}</textarea>
                                </div>

                                <div>
                                    <label class="form-label">Palabras clave</label>
                                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-6">
                            <div class="rounded-3 border p-3 bg-white h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <p class="small text-uppercase fw-semibold text-muted mb-2">Permisos y estado</p>

                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="editable_by_agents" value="1" class="form-check-input" id="editable_by_agents" {{ old('editable_by_agents', $page->editable_by_agents) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="editable_by_agents">Permitir edicion de agentes</label>
                                    </div>

                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="published" value="1" class="form-check-input" id="published" {{ old('published', $page->published) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="published">Publicar pagina</label>
                                    </div>
                                </div>

                                <div class="pt-3 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary btn-sm px-3">
                                        <i class="fas fa-save me-1"></i>Guardar cambios
                                    </button>
                                    <a href="{{ route('pages.show', $page) }}" target="_blank" class="btn btn-outline-dark btn-sm px-3">
                                        <i class="fas fa-eye me-1"></i>Vista publica
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="page-builder-stage shadow-sm rounded-4 overflow-hidden">
                <livewire:page-builder.page-editor :page="$page" />
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

        .page-builder-stage {
            background: #f7f9fc;
        }

        @media (max-width: 1399.98px) {
            .page-meta-card {
                height: auto;
            }
        }
    </style>
</x-admin-layout>
