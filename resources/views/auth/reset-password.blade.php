<x-guest-layout>

    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="mx-auto d-block img-fluid">
    </div>

    <form method="POST" action="{{ route('password.store') }}" x-data="passwordForm()">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus>

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password">Contraseña</label>

            <div class="position-relative">
                <input id="password"
                       x-model="password"
                       :type="showPassword ? 'text' : 'password'"
                       name="password"
                       class="form-control pe-5 @error('password') is-invalid @enderror"
                       required>

                <!-- Toggle -->
                <button type="button"
                        @click="showPassword = !showPassword"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                    <span x-text="showPassword ? '🙈' : '👁'"></span>
                </button>
            </div>

            <!-- Strength bar -->
            <div class="mt-2">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar"
                         role="progressbar"
                         :style="`width: ${strength}%`"
                         :class="{
                            'bg-danger': strength <= 25,
                            'bg-warning': strength > 25 && strength <= 50,
                            'bg-info': strength > 50 && strength <= 75,
                            'bg-success': strength > 75
                         }">
                    </div>
                </div>
                <small class="text-muted" x-text="strengthText"></small>
            </div>

            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation">Confirmar contraseña</label>

            <div class="position-relative">
                <input id="password_confirmation"
                       :type="showPasswordConfirm ? 'text' : 'password'"
                       name="password_confirmation"
                       class="form-control pe-5 @error('password_confirmation') is-invalid @enderror"
                       required>

                <!-- Toggle -->
                <button type="button"
                        @click="showPasswordConfirm = !showPasswordConfirm"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                    <span x-text="showPasswordConfirm ? '🙈' : '👁'"></span>
                </button>
            </div>

            @error('password_confirmation')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Actions -->
        <div class="d-grid mb-3">
            <button type="submit"
                    class="btn btn-primary rounded-pill"
                    :disabled="loading">
                <span x-show="!loading">Restablecer contraseña</span>
                <span x-show="loading">Procesando...</span>
            </button>
        </div>

        <div class="text-center small">
            <a href="{{ route('login') }}">
                Volver al login
            </a>
        </div>

    </form>

    <!-- Alpine logic -->
    <script>
        function passwordForm() {
            return {
                password: '',
                showPassword: false,
                showPasswordConfirm: false,
                loading: false,

                get strength() {
                    let score = 0;
                    if (this.password.length >= 8) score += 25;
                    if (/[A-Z]/.test(this.password)) score += 25;
                    if (/[0-9]/.test(this.password)) score += 25;
                    if (/[^A-Za-z0-9]/.test(this.password)) score += 25;
                    return score;
                },

                get strengthText() {
                    if (this.strength <= 25) return 'Débil';
                    if (this.strength <= 50) return 'Media';
                    if (this.strength <= 75) return 'Fuerte';
                    return 'Muy fuerte';
                }
            }
        }
    </script>

</x-guest-layout>