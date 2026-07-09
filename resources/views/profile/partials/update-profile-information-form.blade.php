<section>

    <!-- Header -->
    <div class="mb-4">
        <h2 class="h5 fw-semibold text-dark">
            {{ __('Profile Information') }}
        </h2>

        <p class="small text-muted mb-0">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </div>

    <!-- Hidden verification form -->
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Form -->
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">
                {{ __('Name') }}
            </label>

            <input id="name"
                   name="name"
                   type="text"
                   value="{{ old('name', $user->name) }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required autofocus autocomplete="name">

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="form-label">
                {{ __('Email') }}
            </label>

            <input id="email"
                   name="email"
                   type="email"
                   value="{{ old('email', $user->email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autocomplete="username">

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

            <!-- Verification -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())

                <div class="mt-2">

                    <p class="small text-muted mb-1">
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button form="send-verification"
                            class="btn btn-link p-0 small">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success small mt-2 py-2 px-3 mb-0">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif

                </div>

            @endif

        </div>

        <!-- Actions -->
        <div class="d-flex align-items-center gap-3">

            <button type="submit" class="btn btn-primary rounded-pill">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span class="small text-muted">
                    {{ __('Saved.') }}
                </span>
            @endif

        </div>

    </form>

</section>