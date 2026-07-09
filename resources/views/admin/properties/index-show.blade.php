{{-- SHOW partial — sólo contenido, sin wrapper --}}
<div class="mb-4">

    {{-- Imagen principal --}}
    <template x-if="property.main_image_url">
        <img :src="property.main_image_url"
             class="w-100 rounded-3 mb-4 object-fit-cover"
             style="height: 180px;">
    </template>

    {{-- Título + operación --}}
    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
        <h2 class="h6 fw-semibold text-dark mb-0" x-text="property.title"></h2>
        <span class="badge rounded-pill badge-accent text-capitalize" x-text="property.operation"></span>
    </div>

    {{-- Precio --}}
    <p class="fw-semibold mb-3" style="font-size: 1.1rem; color: var(--accent, #bf9180);"
       x-text="property.price ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(property.price) : '—'">
    </p>

    {{-- Detalles --}}
    <div class="row g-2 mb-3" style="font-size: 0.82rem; color: #666;">
        <div class="col-6 d-flex align-items-center gap-2">
            <i class="fa-solid fa-tag" style="color: var(--accent);"></i>
            <span x-text="property.type ? property.type.charAt(0).toUpperCase() + property.type.slice(1) : '—'"></span>
        </div>
        <div class="col-6 d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-dot" style="color: var(--accent);"></i>
            <span x-text="property.status ? property.status.charAt(0).toUpperCase() + property.status.slice(1) : '—'"></span>
        </div>
        <template x-if="property.surface">
            <div class="col-6 d-flex align-items-center gap-2">
                <i class="fa-solid fa-vector-square" style="color: var(--accent);"></i>
                <span x-text="property.surface + ' m²'"></span>
            </div>
        </template>
        <template x-if="property.rooms">
            <div class="col-6 d-flex align-items-center gap-2">
                <i class="fa-solid fa-bed" style="color: var(--accent);"></i>
                <span x-text="property.rooms + ' hab.'"></span>
            </div>
        </template>
        <template x-if="property.bathrooms">
            <div class="col-6 d-flex align-items-center gap-2">
                <i class="fa-solid fa-shower" style="color: var(--accent);"></i>
                <span x-text="property.bathrooms + ' baños'"></span>
            </div>
        </template>
    </div>

    {{-- Ubicación --}}
    <template x-if="property.city || property.address">
        <div class="d-flex align-items-start gap-2 p-3 rounded-3 mb-2" style="background: rgba(var(--accent-rgb), 0.07); font-size: 0.82rem;">
            <i class="fa-solid fa-location-dot mt-1" style="color: var(--accent);"></i>
            <div>
                <span x-text="property.address"></span>
                <span x-show="property.address && property.city">, </span>
                <span x-text="property.city"></span>
                <span x-show="property.province" x-text="' (' + property.province + ')'"></span>
            </div>
        </div>
    </template>

    {{-- Descripción --}}
    <template x-if="property.description">
        <p class="text-muted mb-0" style="font-size: 0.82rem; line-height: 1.6;" x-text="property.description"></p>
    </template>

    {{-- Imágenes secundarias --}}
    <template x-if="property.images && property.images.filter(i => i.type === 'secondary').length > 0">
        <div class="mt-4">
            <p class="small fw-semibold text-muted text-uppercase mb-2" style="letter-spacing:.05em;">
                Imágenes
            </p>
            <div class="row g-2">
                <template x-for="img in property.images.filter(i => i.type === 'secondary')" :key="img.id">
                    <div class="col-4">
                        <img :src="img.url" :alt="property.title"
                             class="w-100 rounded-2 object-fit-cover"
                             style="height: 80px; cursor: pointer;"
                             @click="$dispatch('lightbox-open', { src: img.url })">
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Planos --}}
    <template x-if="property.images && property.images.filter(i => i.type === 'floor_plan').length > 0">
        <div class="mt-4">
            <p class="small fw-semibold text-muted text-uppercase mb-2" style="letter-spacing:.05em;">
                Planos
            </p>
            <div class="row g-2">
                <template x-for="img in property.images.filter(i => i.type === 'floor_plan')" :key="img.id">
                    <div class="col-6">
                        <img :src="img.url" :alt="'Plano — ' + property.title"
                             class="w-100 rounded-2 object-fit-contain border"
                             style="height: 100px; background: #f8f8f8; cursor: pointer;"
                             @click="$dispatch('lightbox-open', { src: img.url })">
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Mapa de ubicación --}}
    <template x-if="property.latitude && property.longitude">
        <div class="mt-4">
            <p class="small fw-semibold text-muted text-uppercase mb-2" style="letter-spacing:.05em;">
                Localización exacta
            </p>
            <div :id="'property-map-' + property.id" class="w-100 rounded-2 border" style="height: 280px;"></div>
        </div>
    </template>
    <template x-if="!property.latitude || !property.longitude">
        <template x-if="property.city || property.address">
            <div class="mt-4">
                <p class="small fw-semibold text-muted text-uppercase mb-2" style="letter-spacing:.05em;">
                    Localización
                </p>
                <a :href="'https://www.google.com/maps/search/' + encodeURIComponent((property.address || '') + ' ' + (property.city || '') + ' ' + (property.province || ''))"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-sm btn-outline-secondary w-100 d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-map me-2"></i>Ver en Google Maps
                </a>
            </div>
        </template>
    </template>

</div>
