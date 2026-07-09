<form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" x-data="passwordRules()">
    @csrf

    <input type="hidden" name="_drawer_mode" value="create">

    <!-- Role -->
    <div class="mb-3">
        <label for="role_id" class="form-label">
            {{ __('admin_users.field_role') }}
        </label>

        <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>

        @error('role_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label">
            {{ __('admin_users.field_name') }}
        </label>

        <input id="name" name="name" type="text" value="{{ old('name') }}"
            class="form-control @error('name') is-invalid @enderror" required autofocus>

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label">
            {{ __('admin_users.field_email') }}
        </label>

        <input id="email" name="email" type="email" value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror" required>

        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">
            {{ __('admin_users.field_password') }}
        </label>

        <input id="password" name="password" type="password"
            class="form-control @error('password') is-invalid @enderror" x-model="password" @input="check()" required
            autocomplete="new-password">

        <!-- Checklist -->
        <ul class="small d-flex flex-wrap mt-2 ps-0 list-unstyled column-gap-3 row-gap-0">
            <li :class="okLen ? 'text-success' : 'text-muted'">
                <span x-text="okLen ? '✓' : '•'"></span>
                {{ __('Mínimo 8 caracteres') }}
            </li>

            <li :class="okUpper ? 'text-success' : 'text-muted'">
                <span x-text="okUpper ? '✓' : '•'"></span>
                {{ __('Una mayúscula') }}
            </li>

            <li :class="okLower ? 'text-success' : 'text-muted'">
                <span x-text="okLower ? '✓' : '•'"></span>
                {{ __('Una minúscula') }}
            </li>

            <li :class="okNum ? 'text-success' : 'text-muted'">
                <span x-text="okNum ? '✓' : '•'"></span>
                {{ __('Un número') }}
            </li>
        </ul>

        @error('password')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Avatar -->
    <div class="mb-4">
        <label class="form-label">
            Avatar
            <span class="d-block small text-muted">
                Tamaño máximo permitido: {{ ini_get('upload_max_filesize') }}
            </span>
        </label>

        <input type="file" name="avatar" class="form-control">
    </div>

    <!-- Submit -->
    <div class="d-grid">
        <button type="submit" class="btn btn-accent rounded-pill"
            x-bind:disabled="!(okLen && okUpper && okLower && okNum)">
            {{ __('admin_users.create') }}
        </button>
    </div>

</form>
