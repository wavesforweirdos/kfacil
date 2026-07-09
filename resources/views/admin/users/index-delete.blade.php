<div class="modal-content rounded-4 shadow text-center p-4 bg-white">

    <!-- Title -->
    <h5 class="fw-semibold mb-3">
        {{ __('admin_users.delete_confirm') }}
    </h5>

    <!-- Actions -->
    <div class="d-flex justify-content-center gap-2 mt-4">

        <!-- Delete -->
        <form method="POST" :action="'/admin/users/' + deleteId">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                {{ __('admin_users.delete') }}
            </button>
        </form>

        <!-- Close -->
        <button type="button" class="btn btn-accent rounded-pill px-4" @click="confirmDelete=false">
            {{ __('admin_users.close') }}
        </button>

    </div>

</div>
