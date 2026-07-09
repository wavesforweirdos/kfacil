<x-admin-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold text-dark">Crear nueva pagina</h2>
    </x-slot>

    <div class="py-1 bg-light min-vh-100 accent-blue">
        <div class="container-fluid px-3 px-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 p-lg-4">
                    <form action="{{ route('pages.store') }}" method="POST" class="row g-3">
                        @csrf

                        <div class="col-12 col-lg-6">
                            <label class="form-label">Titulo *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="form-control form-control-sm @error('title') is-invalid @enderror" placeholder="Ej: Pagina de inicio">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label">Slug *</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" required class="form-control form-control-sm @error('slug') is-invalid @enderror" placeholder="Ej: inicio">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Tipo de pagina *</label>
                            <select name="type" required class="form-select form-select-sm @error('type') is-invalid @enderror">
                                <option value="">-- Selecciona un tipo --</option>
                                <option value="home" {{ old('type') === 'home' ? 'selected' : '' }}>Inicio</option>
                                <option value="servicios" {{ old('type') === 'servicios' ? 'selected' : '' }}>Servicios</option>
                                <option value="contacto" {{ old('type') === 'contacto' ? 'selected' : '' }}>Contacto</option>
                                <option value="propiedades" {{ old('type') === 'propiedades' ? 'selected' : '' }}>Propiedades</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripcion</label>
                            <textarea name="description" rows="3" class="form-control form-control-sm" placeholder="Descripcion general de la pagina">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <hr class="my-1">
                            <h3 class="h6 fw-semibold mt-3 mb-1">SEO</h3>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Meta titulo</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control form-control-sm" maxlength="60" placeholder="Ej: Inicio - K Facil Inmobiliaria">
                            <div class="form-text small">Maximo 60 caracteres</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Meta descripcion</label>
                            <textarea name="meta_description" rows="2" class="form-control form-control-sm" maxlength="160" placeholder="Ej: Encuentra las mejores propiedades inmobiliarias">{{ old('meta_description') }}</textarea>
                            <div class="form-text small">Maximo 160 caracteres</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Palabras clave</label>
                            <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control form-control-sm" placeholder="inmuebles, piso, casa, alquiler">
                        </div>

                        <div class="col-12">
                            <hr class="my-1">
                            <h3 class="h6 fw-semibold mt-3 mb-1">Permisos</h3>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="editable_by_agents" value="1" class="form-check-input" id="editable_by_agents" {{ old('editable_by_agents') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="editable_by_agents">Permitir que los agentes editen esta pagina</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 pt-1">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="fas fa-save me-1"></i>Crear pagina
                            </button>
                            <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary btn-sm px-3">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
