<div x-data="{ preview: null, remove_image: false, deleteImages: [] }">

    <form method="POST" :action="'/' + '{{ auth()->user()->panelPrefix() }}' + '/properties/' + property.id"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="_drawer_mode" value="edit">
        <input type="hidden" name="id" :value="property.id">
        <input type="hidden" name="remove_image" :value="remove_image ? 1 : 0">
        <template x-for="imgId in deleteImages" :key="imgId">
            <input type="hidden" name="delete_images[]" :value="imgId">
        </template>

        {{-- Imagen principal actual / preview --}}
        <div class="mb-3">
            <label class="form-label">{{ __('admin_properties.field_image') }}</label>

            <template x-if="preview">
                <img :src="preview" class="w-100 rounded-3 mb-2 object-fit-cover" style="height:140px;">
            </template>
            <template x-if="!preview && property.main_image_url && !remove_image">
                <img :src="property.main_image_url" class="w-100 rounded-3 mb-2 object-fit-cover" style="height:140px;">
            </template>
            <template x-if="!preview && (!property.main_image_url || remove_image)">
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-2 text-muted small"
                    style="height:80px; background: rgba(var(--accent-rgb), 0.07);">
                    Sin imagen
                </div>
            </template>

            <input type="file" name="main_image" accept="image/*" class="form-control form-control-sm"
                @change="preview = URL.createObjectURL($event.target.files[0]); remove_image = false;">

            <div class="d-flex gap-3 mt-1">
                <button type="button" class="btn btn-link text-danger p-0 small"
                    x-show="property.main_image_url || preview" @click="remove_image = true; preview = null;">
                    Eliminar imagen
                </button>
                <button type="button" class="btn btn-link text-muted p-0 small" x-show="remove_image"
                    @click="remove_image = false">
                    Deshacer
                </button>
            </div>
        </div>

        {{-- Imágenes secundarias existentes --}}
        <template
            x-if="property.images && property.images.filter(i => i.type === 'secondary' && !deleteImages.includes(i.id)).length > 0">
            <div class="mb-3">
                <label class="form-label">Imágenes adicionales</label>
                <div class="row g-2">
                    <template
                        x-for="img in property.images.filter(i => i.type === 'secondary' && !deleteImages.includes(i.id))"
                        :key="img.id">
                        <div class="col-4 position-relative">
                            <img :src="img.url" class="w-100 rounded-2 object-fit-cover" style="height:70px;">
                            <button type="button"
                                class="z-1 position-absolute top-0 start-100 translate-middle bg-danger text-white border border-light rounded-circle"
                                @click="deleteImages.push(img.id)">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Añadir imágenes secundarias --}}
        <div class="mb-3">
            <label class="form-label">
                Añadir imágenes adicionales
                <span class="d-block small text-muted">Máx. 10 archivos · 4MB c/u</span>
            </label>
            <input type="file" name="secondary_images[]" accept="image/*" multiple
                class="form-control form-control-sm">
            @error('secondary_images.*')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Planos existentes --}}
        <template
            x-if="property.images && property.images.filter(i => i.type === 'floor_plan' && !deleteImages.includes(i.id)).length > 0">
            <div class="mb-3">
                <label class="form-label">Planos</label>
                <div class="row g-2">
                    <template
                        x-for="img in property.images.filter(i => i.type === 'floor_plan' && !deleteImages.includes(i.id))"
                        :key="img.id">
                        <div class="col-6 position-relative">
                            <img :src="img.url" class="w-100 rounded-2 object-fit-contain border"
                                style="height:80px; background:#f8f8f8;">
                            <button type="button"
                                class="z-1 position-absolute top-0 start-100 translate-middle bg-danger text-white border border-light rounded-circle"
                                style="width:20px;height:20px;font-size:10px;transform:translate(40%,-40%);"
                                @click="deleteImages.push(img.id)">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Añadir planos --}}
        <div class="mb-3">
            <label class="form-label">
                Añadir planos
                <span class="d-block small text-muted">Máx. 5 archivos · 8MB c/u</span>
            </label>
            <input type="file" name="floor_plans[]" accept="image/*" multiple class="form-control form-control-sm">
            @error('floor_plans.*')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Referencia --}}
        @include('admin.properties._reference-field', [
            'mode' => 'edit',
            'current' => '', // Se actualiza desde Alpine.js
        ])

        {{-- Título --}}
        <div class="mb-3">
            <label class="form-label">{{ __('admin_properties.field_title') }}</label>
            <input type="text" name="title" x-model="property.title"
                class="form-control @error('title') is-invalid @enderror" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tipo + Operación --}}
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_type') }}</label>
                <select name="type_id" x-model="property.type_id" class="form-select @error('type_id') is-invalid @enderror"
                    required>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}">{{ $type->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_operation') }}</label>
                <select name="operation_id" x-model="property.operation_id"
                    class="form-select @error('operation_id') is-invalid @enderror" required>
                    @foreach ($operations as $operation)
                        <option value="{{ $operation->id }}">{{ $operation->label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Estado + Precio --}}
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_status') }}</label>
                <select name="status_id" x-model="property.status_id"
                    class="form-select @error('status_id') is-invalid @enderror" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_price') }}</label>
                <input type="number" name="price" x-model="property.price" min="0" step="0.01"
                    class="form-control @error('price') is-invalid @enderror" required>
            </div>
        </div>

        {{-- Superficie + Habitaciones + Baños --}}
        <div class="row g-3 mb-3">
            <div class="col-4">
                <label class="form-label">{{ __('admin_properties.field_surface') }}</label>
                <input type="number" name="surface" x-model="property.surface" min="1"
                    class="form-control">
            </div>
            <div class="col-4">
                <label class="form-label">{{ __('admin_properties.field_rooms') }}</label>
                <input type="number" name="rooms" x-model="property.rooms" min="0" class="form-control">
            </div>
            <div class="col-4">
                <label class="form-label">{{ __('admin_properties.field_bathrooms') }}</label>
                <input type="number" name="bathrooms" x-model="property.bathrooms" min="0"
                    class="form-control">
            </div>
        </div>

        {{-- Ubicación --}}
        <div class="mb-3">
            <label class="form-label">{{ __('admin_properties.field_address') }}</label>
            <input type="text" name="address" x-model="property.address"
                placeholder="{{ __('admin_properties.field_address_placeholder') }}" class="form-control">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_city') }}</label>
                <input type="text" name="city" x-model="property.city" class="form-control">
            </div>
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_locality') }}</label>
                <input type="text" name="locality" x-model="property.locality"
                    placeholder="{{ __('admin_properties.field_locality_placeholder') }}" class="form-control">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_province') }}</label>
                <input type="text" name="province" x-model="property.province" class="form-control">
            </div>
            <div class="col-6">
                <label class="form-label">{{ __('admin_properties.field_country') }}</label>
                <input type="text" name="country" x-model="property.country" class="form-control">
            </div>
        </div>

        {{-- Coordenadas --}}
        <div x-data="{
            mapsUrl: '',
            parseUrl() {
                const m = this.mapsUrl.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
                if (!m) {
                    const q = this.mapsUrl.match(/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
                    if (q) {
                        $root.property.latitude = q[1];
                        $root.property.longitude = q[2];
                        return;
                    }
                } else {
                    $root.property.latitude = m[1];
                    $root.property.longitude = m[2];
                }
            }
        }" class="mb-3">
            <label class="form-label d-flex align-items-center justify-content-between">
                {{ __('admin_properties.field_coordinates') }}
                <span class="text-muted small fw-normal">{{ __('admin_properties.field_coordinates_hint') }}</span>
            </label>
            <div class="input-group input-group-sm mb-2">
                <span class="input-group-text"><i class="fa-brands fa-google" style="font-size:11px;"></i></span>
                <input type="text" x-model="mapsUrl" placeholder="{{ __('admin_properties.field_maps_url') }}"
                    class="form-control">
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="parseUrl()">
                    <i class="fa-solid fa-location-crosshairs"></i>
                </button>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <label
                        class="form-label small text-muted mb-1">{{ __('admin_properties.field_latitude') }}</label>
                    <input type="number" name="latitude" x-model="property.latitude" step="0.0000001"
                        placeholder="39.4699..." class="form-control form-control-sm">
                </div>
                <div class="col-6">
                    <label
                        class="form-label small text-muted mb-1">{{ __('admin_properties.field_longitude') }}</label>
                    <input type="number" name="longitude" x-model="property.longitude" step="0.0000001"
                        placeholder="-0.3762..." class="form-control form-control-sm">
                </div>
            </div>
        </div>

        {{-- Descripción --}}
        <div class="mb-4">
            <label class="form-label">{{ __('admin_properties.field_description') }}</label>
            <textarea name="description" rows="3" class="form-control" x-model="property.description"></textarea>
        </div>

        {{-- Actions --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-accent rounded-pill">
                {{ __('admin_properties.save') }}
            </button>
        </div>

    </form>
</div>
