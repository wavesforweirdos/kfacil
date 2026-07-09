{{--
    <x-confirm-modal />

    Escucha el evento window `confirm-show` con detail:
    {
        title:          '¿Eliminar?',
        message:        'Esta acción no se puede deshacer.',
        confirmLabel:   'Eliminar',       // opcional, default 'Confirmar'
        cancelLabel:    'Cancelar',       // opcional
        type:           'danger'|'warning', // opcional, default 'danger'
        url:            '/admin/properties/5',
        method:         'DELETE'|'POST',  // opcional, default 'DELETE'
    }

    Uso desde Alpine:
        $dispatch('confirm-show', {
            title: '¿Seguro?', message: 'No se puede deshacer.',
            url: `/admin/properties/${id}`, method: 'DELETE'
        })
--}}
<div x-data="{
    show: false,
    title: '',
    message: '',
    confirmLabel: 'Confirmar',
    cancelLabel: 'Cancelar',
    type: 'danger',
    showIcon: false,
    actionUrl: '',
    actionMethod: 'DELETE',

    open(detail) {
        this.title        = detail.title        ?? '¿Estás seguro?';
        this.message      = detail.message      ?? 'Esta acción no se puede deshacer.';
        this.confirmLabel = detail.confirmLabel ?? 'Confirmar';
        this.cancelLabel  = detail.cancelLabel  ?? 'Cancelar';
        this.type         = detail.type         ?? 'danger';
        this.showIcon     = detail.showIcon     ?? false;
        this.actionUrl    = detail.url          ?? '';
        this.actionMethod = detail.method       ?? 'DELETE';
        this.show = true;
    },

    submit() {
        this.$refs.confirmForm.submit();
    }
}"
x-on:confirm-show.window="open($event.detail)"
x-show="show" x-cloak
@keydown.escape.window="show = false"
class="confirm-overlay">

    {{-- Backdrop --}}
    <div x-transition:enter="anim-fade-enter-to"
         x-transition:enter-start="anim-fade-enter"
         x-transition:leave="anim-fade-leave-to"
         x-transition:leave-end="anim-fade-leave-to"
         class="confirm-backdrop"
         @click="show = false"></div>

    {{-- Dialog --}}
    <div class="confirm-centerer">
        <div x-transition:enter="anim-dialog-enter-to"
             x-transition:enter-start="anim-dialog-enter"
             x-transition:leave="anim-dialog-leave-to"
             x-transition:leave-end="anim-dialog-leave-to"
             class="confirm-dialog">

            {{-- Icon (optional, showIcon: true to display) --}}
            <div x-show="showIcon"
                 class="confirm-dialog__icon"
                 :class="type === 'danger' ? 'confirm-dialog__icon--danger' : 'confirm-dialog__icon--warning'">
                <i :class="type === 'danger' ? 'fa-solid fa-trash' : 'fa-solid fa-triangle-exclamation'"></i>
            </div>

            {{-- Title --}}
            <p class="confirm-dialog__title" x-text="title"></p>

            {{-- Message --}}
            <p class="confirm-dialog__message" x-text="message"></p>

            {{-- Buttons: confirm action on left, cancel on right --}}
            <div class="confirm-dialog__actions">
                <button type="button"
                        class="btn rounded-pill px-4"
                        :class="type === 'danger' ? 'btn-outline-danger' : 'btn-outline-warning'"
                        @click="submit()"
                        x-text="confirmLabel">
                </button>
                <button type="button"
                        class="btn btn-secondary rounded-pill px-4"
                        @click="show = false"
                        x-text="cancelLabel">
                </button>
            </div>

            {{-- Hidden form --}}
            <form x-ref="confirmForm" method="POST" :action="actionUrl" class="d-none">
                @csrf
                <input type="hidden" name="_method" :value="actionMethod">
            </form>
        </div>
    </div>
</div>
