<section class="mb-4">

    <!-- Header -->
    <div class="mb-4">
        <h2 class="h5 fw-semibold text-dark">
            {{ __('Delete Account') }}
        </h2>

        <p class="small text-muted mb-0">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </div>

    <!-- Trigger button -->
    <button class="btn btn-danger rounded-pill"
            data-bs-toggle="modal"
            data-bs-target="#confirmDeleteModal">
        {{ __('Delete Account') }}
    </button>

    <!-- Modal -->
    <div class="modal fade @if($errors->userDeletion->isNotEmpty()) show d-block @endif"
         id="confirmDeleteModal"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-body p-4">

                        <!-- Title -->
                        <h5 class="fw-semibold text-dark mb-2">
                            {{ __('Are you sure you want to delete your account?') }}
                        </h5>

                        <!-- Description -->
                        <p class="small text-muted">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <!-- Password -->
                        <div class="mt-3">
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="{{ __('Password') }}">

                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <!-- Actions -->
                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>

                        <button type="submit"
                                class="btn btn-danger rounded-pill">
                            {{ __('Delete Account') }}
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>

</section>