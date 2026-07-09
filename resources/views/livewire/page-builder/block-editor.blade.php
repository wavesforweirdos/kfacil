<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="small text-muted text-uppercase fw-semibold">Edicion en vivo</span>
        <div class="small text-muted">
            <span wire:loading.delay>Guardando...</span>
            <span wire:loading.remove>
                @if($lastSavedAt)
                    Guardado {{ $lastSavedAt }}
                @else
                    Sin cambios
                @endif
            </span>
        </div>
    </div>

    <form wire:submit="save" class="vstack gap-3">
        @foreach($schema as $field => $type)
            <div>
                <label class="form-label small text-uppercase mb-1">
                    {{ ucfirst(str_replace('_', ' ', $field)) }}
                </label>

                @if(str_starts_with($type, 'select:'))
                    @php
                        $options = explode(',', substr($type, 7));
                    @endphp
                    <select
                        wire:model.live="data.{{ $field }}"
                        class="form-select form-select-sm"
                    >
                        <option value="">-- Selecciona --</option>
                        @foreach($options as $option)
                            <option value="{{ trim($option) }}">{{ ucfirst(trim($option)) }}</option>
                        @endforeach
                    </select>

                @elseif($type === 'boolean')
                    <div class="form-check">
                        <input type="checkbox" wire:model.live="data.{{ $field }}" class="form-check-input" id="field_{{ $field }}" />
                        <label class="form-check-label small" for="field_{{ $field }}">Activar</label>
                    </div>

                @elseif($type === 'color')
                    <input
                        type="color"
                        wire:model.live="data.{{ $field }}"
                        class="form-control form-control-color form-control-sm"
                    />

                @elseif($type === 'number')
                    <input
                        type="number"
                        wire:model.live.debounce.500ms="data.{{ $field }}"
                        class="form-control form-control-sm"
                    />

                @elseif($type === 'image')
                    <div class="vstack gap-2">
                        @if(!empty($data[$field]))
                            <img src="{{ $data[$field] }}" alt="{{ $field }}" class="img-fluid rounded border" style="max-height: 140px; object-fit: cover;">
                        @endif

                        <input
                            type="file"
                            accept="image/*"
                            wire:model.live="imageUploads.{{ $field }}"
                            class="form-control form-control-sm"
                        />

                        <div class="small text-muted">Sube una imagen desde tu equipo. Se guarda automaticamente.</div>
                    </div>

                @elseif($type === 'text')
                    <textarea
                        wire:model.live.debounce.700ms="data.{{ $field }}"
                        rows="3"
                        class="form-control form-control-sm"
                    ></textarea>

                @elseif($type === 'array')
                    <div class="vstack gap-2">
                        <div class="alert alert-light border small mb-0">
                            Campo array: edita en JSON. Ejemplo navbar links:
                            [{"label":"Inicio","url":"/inicio"},{"label":"Servicios","url":"/servicios"}]
                        </div>
                        <textarea
                            wire:model.live.debounce.800ms="data.{{ $field }}"
                            rows="4"
                            placeholder='[{"title": "Item 1"}]'
                            class="form-control form-control-sm font-monospace"
                        ></textarea>
                    </div>

                @else
                    <input
                        type="text"
                        wire:model.live.debounce.600ms="data.{{ $field }}"
                        class="form-control form-control-sm"
                    />
                @endif

                @error("data.$field")
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        @endforeach

        <button
            type="submit"
            class="btn btn-primary btn-sm w-100"
        >
            <i class="fas fa-save me-2"></i>Guardar
        </button>
    </form>

    @if(session('success'))
        <div class="alert alert-success mt-3 mb-0" role="alert">
            {{ session('success') }}
        </div>
    @endif
</div>
