<div x-data="{ preview: null, remove_avatar: false }">

    <form method="POST" :action="'/admin/users/' + user.id" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="_drawer_mode" value="edit">
        <input type="hidden" name="id" :value="user.id">
        <input type="hidden" name="remove_avatar" :value="remove_avatar ? 1 : 0">

        <!-- AVATAR -->
        <div class="d-flex align-items-center gap-4 mb-4">

            <!-- Avatar preview -->
            <div class="position-relative">

                <!-- Preview nueva -->
                <template x-if="preview">
                    <img :src="preview"
                         class="rounded-circle border"
                         style="width:80px; height:80px; object-fit:cover;">
                </template>

                <!-- Actual -->
                <template x-if="!preview && user.avatar && !remove_avatar">
                    <img :src="'/storage/' + user.avatar"
                         class="rounded-circle border"
                         style="width:80px; height:80px; object-fit:cover;">
                </template>

                <!-- Iniciales -->
                <template x-if="!preview && (!user.avatar || remove_avatar)">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-semibold"
                         style="width:80px; height:80px; background-color: var(--accent, #2e6fa3);"
                         x-text="user.name
                            ? user.name.split(' ')
                                .map(w => w.charAt(0).toUpperCase())
                                .slice(0,2)
                                .join('')
                            : ''">
                    </div>
                </template>

            </div>

            <!-- Upload -->
            <div class="flex-grow-1">

                <label class="form-label">
                    Avatar
                    <span id="avatar-error" class="d-block small text-muted">
                        Tamaño máximo permitido: {{ ini_get('upload_max_filesize') }}
                    </span>
                </label>

                <input type="file"
                       name="avatar"
                       accept="image/*"
                       class="form-control"
                       @change="
                            const file = $event.target.files[0];
                            const max = 2 * 1024 * 1024;
                            const errorContainer = document.querySelector('#avatar-error');

                            if (file.size > max) {
                                $event.target.value = '';
                                errorContainer.innerHTML = 'El archivo supera el tamaño máximo permitido (2MB)';
                            } else {
                                errorContainer.innerHTML = '';
                            }

                            errorContainer.classList.toggle('text-danger', file.size > max);
                            errorContainer.classList.toggle('text-muted', file.size <= max);

                            remove_avatar = false;
                            preview = URL.createObjectURL(file);
                       ">

                <!-- Actions -->
                <div class="d-flex gap-3 mt-2">

                    <button type="button"
                            class="btn btn-link text-danger p-0 small"
                            x-show="user.avatar || preview"
                            @click="remove_avatar = true; preview = null;">
                        Eliminar avatar
                    </button>

                    <button type="button"
                            class="btn btn-link text-muted p-0 small"
                            x-show="remove_avatar"
                            @click="remove_avatar = false">
                        Deshacer
                    </button>

                </div>

                @error('avatar')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

        <!-- NAME -->
        <div class="mb-3">
            <label class="form-label">Nombre</label>

            <input type="text"
                   name="name"
                   x-model="user.name"
                   class="form-control @error('name') is-invalid @enderror">

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- EMAIL -->
        <div class="mb-3">
            <label class="form-label">Email</label>

            <input type="email"
                   name="email"
                   x-model="user.email"
                   class="form-control @error('email') is-invalid @enderror">

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- ROLE -->
        <div class="mb-4">
            <label class="form-label">Rol</label>

            <select name="role_id"
                    x-model="user.role_id"
                    class="form-select @error('role_id') is-invalid @enderror"
                    required>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>

            @error('role_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- ACTIONS -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-accent rounded-pill">
                {{ __('admin_users.save') }}
            </button>
        </div>

    </form>

</div>