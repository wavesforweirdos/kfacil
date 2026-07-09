<div class="container-fluid p-0 builder-shell" x-data="builderStudio($wire)" x-init="init()">
    @php
        $previewFrameClass = match($previewDevice) {
            'mobile' => 'preview-device-mobile',
            'tablet' => 'preview-device-tablet',
            default => 'preview-device-desktop',
        };
    @endphp

    <div class="builder-studio-header px-3 px-lg-4 py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-3">
            <span class="builder-studio-dot"></span>
            <div>
                <p class="mb-0 fw-semibold text-white">Page Studio<span class="mx-2">-</span><span class="builder-studio-subtitle">{{ $page->title }}</span></p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="btn-group btn-group-sm" role="group" aria-label="Dispositivo de preview">
                <button type="button" wire:click="setPreviewDevice('desktop')" class="btn {{ $previewDevice === 'desktop' ? 'btn-light text-dark' : 'btn-outline-light' }}">
                    <i class="fa fa-desktop"></i>
                </button>
                <button type="button" wire:click="setPreviewDevice('tablet')" class="btn {{ $previewDevice === 'tablet' ? 'btn-light text-dark' : 'btn-outline-light' }}">
                    <i class="fa fa-tablet-alt"></i>
                </button>
                <button type="button" wire:click="setPreviewDevice('mobile')" class="btn {{ $previewDevice === 'mobile' ? 'btn-light text-dark' : 'btn-outline-light' }}">
                    <i class="fa fa-mobile-alt"></i>
                </button>
            </div>

            <button type="button" wire:click="refreshPreview" class="btn btn-sm btn-outline-light px-3">
                <i class="fa fa-rotate-right me-1"></i>Refrescar
            </button>
            <a href="{{ route('pages.show', $page) }}" target="_blank" class="btn btn-sm btn-light text-dark px-3">
                <i class="fa fa-arrow-up-right-from-square me-1"></i>Abrir
            </a>
        </div>
    </div>

    <div class="row g-0 page-editor-wrap">
        <div class="col-12 col-xl-2 border-end bg-white builder-panel-left">
            <div class="p-3 border-bottom">
                <h3 class="h6 mb-1 fw-semibold">Componentes</h3>
                <p class="text-muted small mb-0 text-uppercase">Tipo: {{ $page->type }}</p>
            </div>

            <div class="p-3 d-grid gap-2">
                @foreach($pageComponents as $componentKey => $componentLabel)
                    <button
                        wire:click="addBlock('{{ $componentKey }}')"
                        class="btn btn-outline-primary btn-sm text-start px-3"
                    >
                        <i class="fa fa-plus me-2"></i>{{ $componentLabel }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="col-12 col-xl-7 builder-center-stage border-end">
            <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 mb-1 fw-semibold">{{ $page->title }}</h2>
                    <p class="small text-muted mb-0">Vista previa en directo + estructura de bloques</p>
                </div>
                <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">{{ strtoupper($previewDevice) }}</span>
            </div>

            <div class="p-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold text-uppercase text-muted">Preview</span>
                        <span class="badge rounded-pill bg-info-subtle text-info-emphasis">En directo</span>
                    </div>
                    <div class="builder-preview-stage">
                        <div class="preview-device-frame {{ $previewFrameClass }}">
                            <div class="ratio ratio-16x9 bg-white">
                                <iframe
                                    title="Vista previa de pagina"
                                    src="{{ route('pages.show', $page) }}?builder_preview={{ $previewVersion }}"
                                    class="w-100 h-100 border-0"
                                    loading="lazy"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-2 px-3">
                        <span class="small fw-semibold text-uppercase text-muted">Bloques de la pagina</span>
                    </div>

                    <div class="card-body p-3" data-block-sortable>
                        @forelse($blocks as $block)
                            @php
                                $schema = \App\Models\PageBlock::getComponentSchema($block['component']);
                                $quickFields = ['brand', 'title', 'subtitle', 'description', 'cta_text', 'cta_url', 'button_text', 'button_url', 'image'];
                                $availableQuickFields = array_values(array_filter($quickFields, fn ($field) => array_key_exists($field, $schema) || array_key_exists($field, $block['data'] ?? [])));
                            @endphp
                            <div
                                wire:key="block-{{ $block['id'] }}"
                                data-block-id="{{ $block['id'] }}"
                                class="card mb-2 border {{ (string) $selectedBlockId === (string) $block['id'] ? 'border-primary shadow-sm' : 'border-light-subtle' }}"
                            >
                                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                                    <button
                                        type="button"
                                        class="btn btn-link p-0 text-decoration-none text-start flex-grow-1"
                                        wire:click="selectBlock('{{ $block['id'] }}')"
                                    >
                                        <div class="fw-semibold text-dark small text-uppercase">
                                            <i class="fa fa-grip-vertical text-muted me-2"></i>
                                            {{ ucfirst(str_replace('_', ' ', $block['component'])) }}
                                        </div>
                                        <div class="small text-muted">Orden: {{ $loop->index + 1 }}</div>
                                    </button>

                                    <div class="d-flex gap-2 ms-2">
                                        <button wire:click="duplicateBlock({{ $block['id'] }})" class="btn btn-sm btn-outline-secondary" title="Duplicar">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                        <button wire:click="deleteBlock({{ $block['id'] }})" wire:confirm="Estas seguro de que deseas eliminar este bloque?" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                @if((string) $selectedBlockId === (string) $block['id'] && count($availableQuickFields) > 0)
                                    <div class="px-3 pb-3 pt-1 border-top bg-light-subtle quick-edit-panel">
                                        <p class="small fw-semibold text-uppercase text-muted mb-2">Edicion rapida</p>
                                        <div class="row g-2">
                                            @foreach($availableQuickFields as $quickField)
                                                <div class="col-12 {{ in_array($quickField, ['title', 'subtitle', 'description']) ? 'col-xl-12' : 'col-xl-6' }}">
                                                    <label class="form-label small text-uppercase mb-1">{{ str_replace('_', ' ', $quickField) }}</label>

                                                    @if($quickField === 'description')
                                                        <textarea
                                                            rows="2"
                                                            class="form-control form-control-sm"
                                                            wire:change="updateQuickField({{ $block['id'] }}, '{{ $quickField }}', $event.target.value)"
                                                        >{{ data_get($block, 'data.' . $quickField, '') }}</textarea>
                                                    @else
                                                        <input
                                                            type="text"
                                                            value="{{ data_get($block, 'data.' . $quickField, '') }}"
                                                            class="form-control form-control-sm"
                                                            wire:change="updateQuickField({{ $block['id'] }}, '{{ $quickField }}', $event.target.value)"
                                                        >
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-light border text-center mb-0">
                                No hay bloques aun. Agrega uno desde el panel izquierdo.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-3 bg-white builder-panel-right" x-data="{ tab: 'general' }">
            @if($selectedBlockId && ($selectedBlock = collect($blocks)->firstWhere('id', $selectedBlockId)))
                @php
                    $selectedSchema = \App\Models\PageBlock::getComponentSchema($selectedBlock['component']);
                    $styleCandidates = ['layout', 'height', 'columns', 'view_type', 'background_color', 'text_color', 'show_cta', 'show_map'];
                    $styleFields = array_values(array_filter($styleCandidates, fn ($field) => array_key_exists($field, $selectedSchema)));
                @endphp
                <div class="p-3 border-bottom">
                    <h3 class="h6 mb-1 fw-semibold">Inspector</h3>
                    <p class="small text-muted mb-0">{{ ucfirst(str_replace('_', ' ', $selectedBlock['component'])) }}</p>
                </div>

                <div class="px-3 pt-3 pb-0 border-bottom bg-white">
                    <div class="nav nav-tabs nav-fill inspector-tabs" role="tablist">
                        <button type="button" class="nav-link" :class="tab === 'general' ? 'active' : ''" @click="tab = 'general'">General</button>
                        <button type="button" class="nav-link" :class="tab === 'style' ? 'active' : ''" @click="tab = 'style'">Style</button>
                        <button type="button" class="nav-link" :class="tab === 'advanced' ? 'active' : ''" @click="tab = 'advanced'">Advanced</button>
                    </div>
                </div>

                <div class="p-3">
                    <div x-show="tab === 'general'" x-transition>
                        <livewire:page-builder.block-editor :block="App\Models\PageBlock::find($selectedBlockId)" :key="'block-editor-'.$selectedBlockId" />
                    </div>

                    <div x-show="tab === 'style'" x-transition class="vstack gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-semibold text-uppercase text-muted">Ajustes basicos de estilo</span>
                            <span class="badge rounded-pill bg-light text-muted">Rapido</span>
                        </div>

                        @if(count($styleFields) > 0)
                            @foreach($styleFields as $styleField)
                                <div>
                                    <label class="form-label small text-uppercase mb-1">{{ str_replace('_', ' ', $styleField) }}</label>

                                    @if($selectedSchema[$styleField] === 'boolean')
                                        <div class="form-check form-switch">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                {{ data_get($selectedBlock, 'data.' . $styleField, false) ? 'checked' : '' }}
                                                wire:change="updateBlockOption({{ $selectedBlock['id'] }}, '{{ $styleField }}', $event.target.checked)"
                                            >
                                            <label class="form-check-label small">Activar</label>
                                        </div>
                                    @elseif(str_starts_with($selectedSchema[$styleField], 'select:'))
                                        @php
                                            $options = explode(',', substr($selectedSchema[$styleField], 7));
                                            $current = data_get($selectedBlock, 'data.' . $styleField, '');
                                        @endphp
                                        <select
                                            class="form-select form-select-sm"
                                            wire:change="updateBlockOption({{ $selectedBlock['id'] }}, '{{ $styleField }}', $event.target.value)"
                                        >
                                            <option value="">Selecciona...</option>
                                            @foreach($options as $option)
                                                @php $option = trim($option); @endphp
                                                <option value="{{ $option }}" {{ (string) $current === (string) $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($selectedSchema[$styleField] === 'color')
                                        <input
                                            type="color"
                                            class="form-control form-control-color form-control-sm"
                                            value="{{ data_get($selectedBlock, 'data.' . $styleField, '#1f2a8a') }}"
                                            wire:change="updateBlockOption({{ $selectedBlock['id'] }}, '{{ $styleField }}', $event.target.value)"
                                        >
                                    @else
                                        <input
                                            type="text"
                                            class="form-control form-control-sm"
                                            value="{{ data_get($selectedBlock, 'data.' . $styleField, '') }}"
                                            wire:change="updateBlockOption({{ $selectedBlock['id'] }}, '{{ $styleField }}', $event.target.value)"
                                        >
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-light border small mb-0">
                                Este bloque no tiene opciones de estilo rapido.
                            </div>
                        @endif
                    </div>

                    <div x-show="tab === 'advanced'" x-transition>
                        <div class="alert alert-light border small mb-2">
                            Edicion avanzada: usa el campo <strong>array</strong> para estructuras complejas como menus, galerias o items.
                        </div>
                        <div class="small text-muted">
                            Ejemplo JSON:
                        </div>
                        <pre class="small bg-light border rounded p-2 mt-2 mb-0">[{"label":"Inicio","url":"/inicio"},{"label":"Servicios","url":"/servicios"}]</pre>
                    </div>
                </div>
            @else
                <div class="p-3 text-muted small">
                    <i class="fa fa-mouse-pointer me-2"></i>Selecciona un bloque para editar su contenido.
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    function builderStudio($wire) {
        return {
            sortable: null,

            init() {
                this.$nextTick(() => this.setupSortable());
                if (window.Livewire && typeof Livewire.hook === 'function') {
                    Livewire.hook('morph.updated', () => {
                        this.$nextTick(() => this.setupSortable());
                    });
                }
            },

            setupSortable() {
                const list = this.$root.querySelector('[data-block-sortable]');
                if (!list || typeof Sortable === 'undefined') {
                    return;
                }

                if (this.sortable) {
                    this.sortable.destroy();
                }

                this.sortable = Sortable.create(list, {
                    animation: 170,
                    handle: '.fa-grip-vertical',
                    draggable: '[data-block-id]',
                    onEnd: () => {
                        const ids = Array.from(list.querySelectorAll('[data-block-id]'))
                            .map((el) => Number(el.getAttribute('data-block-id')))
                            .filter((id) => !Number.isNaN(id));

                        if (ids.length > 0) {
                            $wire.updateBlockOrder(ids);
                        }
                    },
                });
            },
        };
    }
</script>

<style>
    .builder-studio-header {
        background: linear-gradient(120deg, #1f2a8a 0%, #2440bf 45%, #3a6ed8 100%);
        border-top-left-radius: 1.1rem;
        border-top-right-radius: 1.1rem;
        position: sticky;
        top: 0;
        z-index: 5;
    }

    .builder-studio-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #62f5c8;
        box-shadow: 0 0 0 4px rgba(98, 245, 200, 0.2);
    }

    .builder-studio-subtitle {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255, 255, 255, 0.75);
    }

    .page-editor-wrap {
        min-height: 74vh;
        border-bottom-left-radius: 1.1rem;
        border-bottom-right-radius: 1.1rem;
        overflow: hidden;
    }

    .builder-shell .card-header {
        border-bottom: 1px solid #edf1f6;
    }

    .builder-center-stage {
        background: radial-gradient(circle at top left, #f1f6ff 0%, #f7f9fd 45%, #f3f4f8 100%);
    }

    .builder-preview-stage {
        background: linear-gradient(140deg, #eef3fb 0%, #e2eaf6 100%);
        padding: 0.9rem;
    }

    .preview-device-frame {
        margin: 0 auto;
        border-radius: 0.8rem;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(37, 64, 122, 0.18);
        transition: max-width 0.22s ease;
    }

    .preview-device-desktop {
        max-width: 100%;
    }

    .preview-device-tablet {
        max-width: 820px;
    }

    .preview-device-mobile {
        max-width: 420px;
    }

    .builder-panel-left,
    .builder-panel-right {
        background: #ffffff;
    }

    .builder-panel-left .btn {
        font-size: 0.76rem;
    }

    .quick-edit-panel {
        border-color: #e6edf7 !important;
    }

    .inspector-tabs .nav-link {
        border: 0;
        color: #64748b;
        font-size: 0.74rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.05em;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        padding-left: 0.2rem;
        padding-right: 0.2rem;
    }

    .inspector-tabs .nav-link.active {
        color: #2a55cb;
        border-bottom-color: #2a55cb;
        background: transparent;
    }

    @media (max-width: 1199.98px) {
        .builder-studio-header {
            border-radius: 0;
        }

        .page-editor-wrap {
            border-radius: 0;
        }
    }
</style>
