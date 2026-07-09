<form method="POST" action="{{ route(auth()->user()->panelPrefix() . '.properties.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="_drawer_mode" value="create">

    {{-- Referencia --}}
    @include('admin.properties._reference-field', ['mode' => 'create', 'current' => null])

    {{-- Título --}}
    <div class="mb-3">
        <label class="form-label">{{ __('admin_properties.field_title') }}</label>
        <input type="text" name="title" value="{{ old('title') }}"
               class="form-control @error('title') is-invalid @enderror" required autofocus>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Tipo + Operación --}}
    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_type') }}</label>
            <select name="type_id" class="form-select @error('type_id') is-invalid @enderror" required>
                <option value="">Seleccionar tipo...</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->label }}
                    </option>
                @endforeach
            </select>
            @error('type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_operation') }}</label>
            <select name="operation_id" class="form-select @error('operation_id') is-invalid @enderror" required>
                <option value="">Seleccionar operación...</option>
                @foreach ($operations as $operation)
                    <option value="{{ $operation->id }}" {{ old('operation_id') == $operation->id ? 'selected' : '' }}>
                        {{ $operation->label }}
                    </option>
                @endforeach
            </select>
            @error('operation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Estado + Precio --}}
    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_status') }}</label>
            <select name="status_id" class="form-select @error('status_id') is-invalid @enderror" required>
                <option value="">Seleccionar estado...</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ old('status_id', $statuses->first()?->id) == $status->id ? 'selected' : '' }}>
                        {{ $status->label }}
                    </option>
                @endforeach
            </select>
            @error('status_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_price') }}</label>
            <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01"
                   class="form-control @error('price') is-invalid @enderror" required>
            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Superficie + Habitaciones + Baños --}}
    <div class="row g-3 mb-3">
        <div class="col-4">
            <label class="form-label">{{ __('admin_properties.field_surface') }}</label>
            <input type="number" name="surface" value="{{ old('surface') }}" min="1"
                   class="form-control @error('surface') is-invalid @enderror">
        </div>
        <div class="col-4">
            <label class="form-label">{{ __('admin_properties.field_rooms') }}</label>
            <input type="number" name="rooms" value="{{ old('rooms') }}" min="0"
                   class="form-control @error('rooms') is-invalid @enderror">
        </div>
        <div class="col-4">
            <label class="form-label">{{ __('admin_properties.field_bathrooms') }}</label>
            <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" min="0"
                   class="form-control @error('bathrooms') is-invalid @enderror">
        </div>
    </div>

    {{-- Ubicación --}}
    <div class="mb-3">
        <label class="form-label">{{ __('admin_properties.field_address') }}</label>
        <input type="text" name="address" value="{{ old('address') }}"
               placeholder="{{ __('admin_properties.field_address_placeholder') }}"
               class="form-control @error('address') is-invalid @enderror">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_city') }}</label>
            <input type="text" name="city" value="{{ old('city') }}"
                   class="form-control @error('city') is-invalid @enderror">
        </div>
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_locality') }}</label>
            <input type="text" name="locality" value="{{ old('locality') }}"
                   placeholder="{{ __('admin_properties.field_locality_placeholder') }}"
                   class="form-control @error('locality') is-invalid @enderror">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_province') }}</label>
            <input type="text" name="province" value="{{ old('province') }}"
                   class="form-control @error('province') is-invalid @enderror">
        </div>
        <div class="col-6">
            <label class="form-label">{{ __('admin_properties.field_country') }}</label>
            <input type="text" name="country" value="{{ old('country', 'España') }}"
                   class="form-control @error('country') is-invalid @enderror">
        </div>
    </div>

    {{-- Coordenadas --}}
    <div x-data="{
        mapsUrl: '',
        parseUrl() {
            const m = this.mapsUrl.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
            if (!m) {
                const q = this.mapsUrl.match(/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
                if (q) { $refs.lat.value = q[1]; $refs.lng.value = q[2]; return; }
            } else {
                $refs.lat.value = m[1]; $refs.lng.value = m[2];
            }
        }
    }" class="mb-3">
        <label class="form-label d-flex align-items-center justify-content-between">
            {{ __('admin_properties.field_coordinates') }}
            <span class="text-muted small fw-normal">{{ __('admin_properties.field_coordinates_hint') }}</span>
        </label>
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text"><i class="fa-brands fa-google" style="font-size:11px;"></i></span>
            <input type="text" x-model="mapsUrl"
                   placeholder="{{ __('admin_properties.field_maps_url') }}"
                   class="form-control">
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="parseUrl()">
                <i class="fa-solid fa-location-crosshairs"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-6">
                <label class="form-label small text-muted mb-1">{{ __('admin_properties.field_latitude') }}</label>
                <input type="number" name="latitude" x-ref="lat" step="0.0000001"
                       value="{{ old('latitude') }}"
                       placeholder="39.4699..."
                       class="form-control form-control-sm @error('latitude') is-invalid @enderror">
                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-6">
                <label class="form-label small text-muted mb-1">{{ __('admin_properties.field_longitude') }}</label>
                <input type="number" name="longitude" x-ref="lng" step="0.0000001"
                       value="{{ old('longitude') }}"
                       placeholder="-0.3762..."
                       class="form-control form-control-sm @error('longitude') is-invalid @enderror">
                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- Descripción --}}
    <div class="mb-3">
        <label class="form-label">{{ __('admin_properties.field_description') }}</label>
        <textarea name="description" rows="3"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Imagen principal --}}
    <div class="mb-3">
        <label class="form-label">
            {{ __('admin_properties.field_image') }}
            <span class="d-block small text-muted">Máx. 4MB — jpg, png, webp</span>
        </label>
        <input type="file" name="main_image" accept="image/*" class="form-control">
        @error('main_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Imágenes secundarias --}}
    <div class="mb-3">
        <label class="form-label">
            Imágenes adicionales
            <span class="d-block small text-muted">Máx. 10 archivos · 4MB c/u — jpg, png, webp</span>
        </label>
        <input type="file" name="secondary_images[]" accept="image/*" multiple class="form-control">
        @error('secondary_images') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        @error('secondary_images.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Planos --}}
    <div class="mb-4">
        <label class="form-label">
            Planos
            <span class="d-block small text-muted">Máx. 5 archivos · 8MB c/u — jpg, png, webp</span>
        </label>
        <input type="file" name="floor_plans[]" accept="image/*" multiple class="form-control">
        @error('floor_plans') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        @error('floor_plans.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Submit --}}
    <div class="d-grid">
        <button type="submit" class="btn btn-accent rounded-pill">
            {{ __('admin_properties.create') }}
        </button>
    </div>

</form>
