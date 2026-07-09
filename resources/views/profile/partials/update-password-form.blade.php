<section>

    <!-- Header -->
    <div class="mb-4">
        <h2 class="h5 fw-semibold text-dark">
            {{ __('Update Password') }}
        </h2>

        <p class="small text-muted mb-0">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">
                {{ __('Current Password') }}
            </label>

            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   autocomplete="current-password">

            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="update_password_password" class="form-label">
                {{ __('New Password') }}
            </label>

            <input id="update_password_password"
                   name="password"
                   type="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">

            @error('password', 'updatePassword')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label">
                {{ __('Confirm Password') }}
            </label>

            <input id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">

            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Actions -->
        <div class="d-flex align-items-center gap-3">

            <button type="submit" class="btn btn-primary rounded-pill">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="small text-muted">
                    {{ __('Saved.') }}
                </span>
            @endif

        </div>

    </form>

</section>