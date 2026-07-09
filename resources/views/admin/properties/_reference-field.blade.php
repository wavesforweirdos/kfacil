{{--
    Partial: _reference-field.blade.php
    Params:
      $mode     : 'create' | 'edit'
      $current  : current reference value (only for edit)
--}}
<div x-data="{
    refMode: '{{ $mode === 'edit' ? 'keep' : 'auto' }}',
    prefix: '',
    manual: '',
    get preview() {
        if (this.refMode === 'auto')   return '{{ $mode === 'edit' ? '(Nueva aleatoria)' : '(Se generará automáticamente)' }}';
        if (this.refMode === 'prefix') return this.prefix ? this.prefix.toUpperCase() + '-XXXXX' : 'SIGLAS-XXXXX';
        if (this.refMode === 'manual') return this.manual ? this.manual.toUpperCase() : '—';
        @if($mode === 'edit')
        return '{{ $current ?? '' }}';
        @endif
        return '';
    }
}" class="mb-3">

    <label class="form-label">{{ __('admin_properties.field_reference') }}</label>

    {{-- Mode selector --}}
    <div class="d-flex gap-2 mb-2 flex-wrap">

        @if($mode === 'edit')
        <label class="d-flex align-items-center gap-1 small" style="cursor:pointer;">
            <input type="radio" x-model="refMode" value="keep" class="form-check-input m-0">
            Mantener actual
        </label>
        @endif

        <label class="d-flex align-items-center gap-1 small" style="cursor:pointer;">
            <input type="radio" x-model="refMode" value="auto" class="form-check-input m-0">
            Automática
        </label>

        <label class="d-flex align-items-center gap-1 small" style="cursor:pointer;">
            <input type="radio" x-model="refMode" value="prefix" class="form-check-input m-0">
            Con prefijo
        </label>

        <label class="d-flex align-items-center gap-1 small" style="cursor:pointer;">
            <input type="radio" x-model="refMode" value="manual" class="form-check-input m-0">
            Manual
        </label>

    </div>

    {{-- Hidden mode --}}
    <input type="hidden" name="reference_mode" :value="refMode">

    {{-- Prefix input --}}
    <div x-show="refMode === 'prefix'" x-cloak>
        <input type="text" name="reference_prefix" x-model="prefix"
               class="form-control form-control-sm text-uppercase"
               placeholder="Ej: MAR, PLAYA, CENTRO…"
               maxlength="10" style="text-transform:uppercase;"
               @input="prefix = prefix.replace(/[^a-zA-Z]/g, '')">
        <div class="form-text">Solo letras. Se añadirán 5 dígitos aleatorios: <strong x-text="prefix ? prefix.toUpperCase() + '-XXXXX' : 'SIGLAS-XXXXX'"></strong></div>
    </div>

    {{-- Manual input --}}
    <div x-show="refMode === 'manual'" x-cloak>
        <input type="text" name="reference_manual" x-model="manual"
               class="form-control form-control-sm text-uppercase"
               placeholder="Ej: COSTA-MAR-001"
               maxlength="30" style="text-transform:uppercase;"
               @input="manual = manual.toUpperCase()">
        <div class="form-text">Referencia libre. Debe ser única.</div>
    </div>

    {{-- Preview badge --}}
    <div class="mt-2" x-show="refMode !== 'keep'" x-cloak>
        <span class="badge badge-accent-soft rounded-pill" style="font-size:0.75rem; letter-spacing:0.05rem;"
              x-text="preview"></span>
    </div>

    @if($mode === 'edit')
    <div class="mt-2" x-show="refMode === 'keep'">
        <span class="badge badge-accent-soft rounded-pill" style="font-size:0.75rem; letter-spacing:0.05rem;">
            {{ $current ?? '—' }}
        </span>
    </div>
    @endif

</div>
