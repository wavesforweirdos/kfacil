<x-admin-layout>
    <x-slot name="header">
        {{ __('admin_users.title') }}
    </x-slot>

    <div x-data="{
        open: false,
        mode: 'show',
        loading: false,
        user: {},
        okLen: false,
        okUpper: false,
        okLower: false,
        okNum: false,
        preview: null,
        remove_avatar: false,

        async openDrawer(mode, userId = null) {
            this.mode = mode;
            this.user = {};
            this.open = true;

            if (userId) {
                this.loading = true;
                try {
                    const res = await fetch(`{{ url(auth()->user()->panelPrefix() . '/users') }}/${userId}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.user = await res.json();
                } finally {
                    this.loading = false;
                }
            }
        },

        init() {
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.open = false;
            });

            @if($errors->any())
            this.open = true;
            this.mode = @js(old('_drawer_mode', 'create'));

            if (this.mode === 'edit') {
                this.user = {
                    id: @js(old('id')),
                    name: @js(old('name')),
                    email: @js(old('email')),
                };
            }
            @endif
        }
    }" class="py-4 bg-light min-vh-100 accent-teal">

        <div class="container-fluid px-4">

            <x-admin.table-toolbar>
                @if (auth()->user()->role->name === 'admin')
                    <x-admin.table-toolbar-button :label="__('admin_users.create')" @click="openDrawer('create')" />
                @endif
            </x-admin.table-toolbar>

            @php
                $sortUrl = function ($column) use ($sort, $dir) {
                    $sortKey = $column['sort_key'] ?? $column['key'];
                    $nextDir = $sort === $sortKey && $dir === 'asc' ? 'desc' : 'asc';

                    return request()->fullUrlWithQuery([
                        'sort' => $sortKey,
                        'dir' => $nextDir,
                        'page' => 1,
                    ]);
                };

                $columns = [
                    [
                        'key' => 'name',
                        'label' => __('admin_users.table_name'),
                        'placeholder' => __('admin_users.search_name'),
                        'sortable' => true,
                        'searchable' => true,
                        'align' => 'left',
                    ],
                    [
                        'key' => 'email',
                        'label' => __('admin_users.table_email'),
                        'placeholder' => __('admin_users.search_email'),
                        'sortable' => true,
                        'searchable' => true,
                        'align' => 'left',
                    ],
                    [
                        'key' => 'role_id',
                        'label' => __('admin_users.table_role'),
                        'sortable' => true,
                        'sort_key' => 'role',
                        'searchable' => true,
                        'type' => 'select',
                    ],
                ];
            @endphp

            <form method="GET" action="{{ route('admin.users.index') }}" x-data="{
                        t: null,
                        submitNow() { $el.requestSubmit(); },
                        submitDebounced() {
                            clearTimeout(this.t);
                            this.t = setTimeout(() => this.submitNow(), 400);
                        }
                    }">
                        {{-- mantener orden al buscar --}}
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="dir" value="{{ $dir }}">

                <x-admin.data-table :paginator="$users" :show-info="false">
                    <x-slot:thead>
                        <tr class="table-headers">
                            @foreach ($columns as $column)
                                <th class="px-4 py-3">
                                    @if ($column['sortable'])
                                        @php $colSortKey = $column['sort_key'] ?? $column['key']; @endphp
                                        <a href="{{ $sortUrl($column) }}"
                                            class="text-white text-decoration-none d-flex align-items-center gap-1">

                                            {{ $column['label'] }}

                                            <span class="d-inline-flex flex-column lh-1">
                                                <i class="fa-solid fa-caret-up {{ $sort === $colSortKey && $dir === 'asc' ? 'opacity-100' : 'opacity-25' }}"></i>
                                                <i class="fa-solid fa-caret-down {{ $sort === $colSortKey && $dir === 'desc' ? 'opacity-100' : 'opacity-25' }}"></i>
                                            </span>
                                        </a>
                                    @else
                                        {{ $column['label'] }}
                                    @endif
                                </th>
                            @endforeach

                            <th class="px-4 py-3 text-center">
                                {{ __('admin_users.table_actions') }}
                            </th>
                        </tr>
                    </x-slot:thead>

                    @if (auth()->user()->role->name === 'admin')
                        <x-slot:search>
                            @foreach ($columns as $column)
                                <th class="px-3 py-2">

                                    @if ($column['searchable'])
                                        @if (($column['type'] ?? 'text') === 'select' && $column['key'] === 'role_id')
                                            <select name="role_id" @change="submitNow()"
                                                class="form-select form-select-sm border-0 bg-white shadow-none">
                                                <option value="">
                                                    {{ __('admin_users.search_role') ?? 'Todos' }}</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        @selected((string) request('role_id') === (string) $role->id)>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" name="{{ $column['key'] }}"
                                                class="form-control form-control-sm border-0 bg-white shadow-none"
                                                @input="submitDebounced()"
                                                value="{{ request($column['key']) }}"
                                                placeholder="{{ $column['placeholder'] ?? '' }}">
                                        @endif
                                    @endif

                                </th>
                            @endforeach

                            <th class="px-3 py-2 text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button type="submit"
                                        class="btn btn--pill btn-light rounded-pill icon-box"
                                        title="{{ __('admin_users.search') }}">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>

                                    <a href="{{ route('admin.users.index', ['sort' => $sort, 'dir' => $dir]) }}"
                                        class="btn btn--pill btn-light rounded-pill icon-box">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                </div>
                            </th>
                        </x-slot:search>
                    @endif

                    @foreach ($users as $user)
                        <tr class="{{ $user->id === auth()->id() ? 'active' : '' }}">
                            <td class="px-3 py-3">{{ $user->name }}</td>
                            <td class="px-3 py-3 text-muted">{{ $user->email }}</td>

                            <td class="px-3 py-3">
                                <span
                                    class="badge rounded-pill {{ $user->role?->name === 'admin' ? 'badge-accent' : 'badge-accent-soft' }}">
                                    {{ ucfirst($user->role?->name ?? '-') }}
                                </span>
                            </td>

                            <td class="text-center">
                                <x-admin.table-actions>

                                    <!-- SHOW -->
                                    <x-admin.table-action icon="fa-eye" variant="mint" :title="__('admin_users.show')"
                                        @click="openDrawer('show', {{ $user->id }})" />

                                    @if (auth()->user()->role->name === 'admin')
                                        <!-- EDIT -->
                                        <x-admin.table-action icon="fa-pen-to-square" variant="teal"
                                            :title="__('admin_users.edit')"
                                            @click="openDrawer('edit', {{ $user->id }})"
                                             />

                                        <!-- DELETE -->
                                        <x-admin.table-action icon="fa-trash" variant="adobe"
                                            :title="__('admin_users.delete')"
                                            @click="$dispatch('confirm-show', {
                                                title: '{{ __('admin_users.delete_title') }}',
                                                message: '{{ __('admin_users.delete_message') }}',
                                                confirmLabel: '{{ __('admin_users.delete_confirm') }}',
                                                cancelLabel: '{{ __('admin_users.delete_cancel') }}',
                                                type: 'danger',
                                                url: `{{ url(auth()->user()->panelPrefix() . '/users') }}/{{{ $user->id }}}`,
                                                method: 'DELETE'
                                            })" />
                                    @endif

                                </x-admin.table-actions>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </form>

        </div>

        <!-- DRAWER BACKDROP -->
        <div x-show="open" x-cloak
            x-transition:enter="anim-fade-enter-to"
            x-transition:enter-start="anim-fade-enter"
            x-transition:leave="anim-fade-leave-to"
            x-transition:leave-end="anim-fade-leave-to"
            class="offcanvas-backdrop fade show" @click="open=false"></div>

        <!-- DRAWER PANEL -->
        <div :class="{ 'show': open }" class="offcanvas offcanvas-end" style="width: 420px; border-left: 3px solid var(--accent, #2e6fa3);"
            @keydown.escape.window="open=false">

            <!-- Header fijo -->
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom flex-shrink-0">
                <h5 class="fw-semibold mb-0"
                    x-text=" mode==='show' ? '{{ __('admin_users.show_title') }}' : mode==='edit' ? '{{ __('admin_users.edit_title') }}' : '{{ __('admin_users.create_title') }}' ">
                </h5>
                <button class="btn-close" @click="open=false"></button>
            </div>

            <!-- Body scrolleable -->
            <div class="px-4 py-4 overflow-y-auto flex-grow-1">

                <!-- Loading spinner -->
                <div x-show="loading" x-cloak style="display: flex; justify-content: center; padding: 3rem 0;">
                    <div class="spinner-border text-teal" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>

                <div x-show="!loading">

                    @if (auth()->user()->role->name === 'admin')
                        <!-- CREATE MODE -->
                        <template x-if="mode==='create'">
                            @include('admin.users.index-create')
                        </template>
                    @endif

                    <!-- SHOW MODE -->
                    <template x-if="mode==='show'">
                        @include('admin.users.index-show')
                    </template>

                    @if (auth()->user()->role->name === 'admin')
                        <!-- EDIT MODE -->
                        <template x-if="mode==='edit'">
                            @include('admin.users.index-edit')
                        </template>
                    @endif

                </div>
            </div>

        </div>


        @if (auth()->user()->role->name === 'admin')
            <!-- DELETE CONFIRM MODAL -->
            <x-confirm-modal />
            <x-toast-notify />
        @endif

    </div>

</x-admin-layout>

<script>
    function passwordRules() {
        return {
            password: '',

            check() {
                const p = this.password || '';
                this.okLen = p.length >= 8;
                this.okUpper = /[A-Z]/.test(p);
                this.okLower = /[a-z]/.test(p);
                this.okNum = /[0-9]/.test(p);
            },

            init() {
                this.check();
            }
        }
    }
</script>
