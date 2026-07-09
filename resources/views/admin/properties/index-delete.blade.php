<div class="modal-content rounded-4 shadow text-center p-4 bg-white">

    <div class="mb-3" style="font-size: 2rem; color: var(--accent, #bf9180);">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>

    <h5 class="fw-semibold mb-2">{{ __('admin_properties.delete_confirm') }}</h5>
    <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>

    <div class="d-flex justify-content-center gap-2 mt-4">
        <button type="button" class="btn btn-outline-accent rounded-pill" @click="confirmDelete=false">
            {{ __('admin_properties.close') }}
        </button>
        <form method="POST" :action="'/{{ auth()->user()->panelPrefix() }}/properties/' + deleteId">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger rounded-pill">
                {{ __('admin_properties.delete') }}
            </button>
        </form>
    </div>

</div>
