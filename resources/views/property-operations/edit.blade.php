<x-admin-layout>
    <x-slot name="header">
        Editar Tipo de Operacion
    </x-slot>

    <div class="py-4 bg-light min-vh-100 accent-adobe">
        <div class="container-fluid px-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 p-lg-5">
                            @if ($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('property-operations.update', $propertyOperation) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold">Nombre *</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $propertyOperation->name) }}"
                                        class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="label" class="form-label fw-semibold">Etiqueta *</label>
                                    <input type="text" id="label" name="label" value="{{ old('label', $propertyOperation->label) }}"
                                        class="form-control @error('label') is-invalid @enderror" required>
                                    @error('label')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="sort_order" class="form-label fw-semibold">Orden</label>
                                    <input type="number" id="sort_order" name="sort_order"
                                        value="{{ old('sort_order', $propertyOperation->sort_order) }}" min="0"
                                        class="form-control @error('sort_order') is-invalid @enderror">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-4">
                                    <input type="checkbox" id="is_active" class="form-check-input" name="is_active" value="1"
                                        @checked(old('is_active', $propertyOperation->is_active))>
                                    <label for="is_active" class="form-check-label">Activo</label>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('property-operations.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                                    <button type="submit" class="btn btn-accent rounded-pill d-flex align-items-center gap-2 px-4">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        Actualizar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
