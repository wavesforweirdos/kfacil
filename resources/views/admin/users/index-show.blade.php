<div class="d-flex align-items-center gap-3 mb-4">

    <!-- Avatar -->
    <template x-if="user.avatar">
        <img :src="'/storage/' + user.avatar"
             class="rounded-circle border"
             style="width:64px; height:64px; object-fit:cover;">
    </template>

    <!-- Iniciales -->
    <template x-if="!user.avatar">
        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-semibold"
             style="width:64px; height:64px; font-size:18px; background-color: var(--accent, #2e6fa3);"
             x-text="user.name
                ? user.name.split(' ')
                    .map(word => word.charAt(0).toUpperCase())
                    .slice(0,2)
                    .join('')
                : ''">
        </div>
    </template>

    <!-- Info -->
    <div>

        <h2 class="h6 fw-semibold mb-1 text-dark"
            x-text="user.name">
        </h2>

        <p class="small text-muted mb-0"
           x-text="user.email">
        </p>

        <p class="small text-secondary mb-0"
           x-text="user.role 
                ? user.role.name.charAt(0).toUpperCase() + user.role.name.slice(1) 
                : '-'">
        </p>

    </div>

</div>