{{--
    <x-toast-notify />

    Escucha el evento window `toast-show` con detail: { type: 'success'|'error', message: '...' }
    También muestra automáticamente los mensajes de sesión Laravel (success / error).

    Uso desde JS:
        window.dispatchEvent(new CustomEvent('toast-show', {
            detail: { type: 'success', message: 'Hecho!' }
        }));

    Uso desde Alpine:
        $dispatch('toast-show', { type: 'error', message: 'Algo falló' })
--}}
<div x-data="{
    show: false,
    type: 'success',
    message: '',
    timer: null,
    display(type, message, duration = 4000) {
        this.type    = type;
        this.message = message;
        this.show    = true;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.show = false, duration);
    }
}"
x-on:toast-show.window="display($event.detail.type, $event.detail.message, $event.detail.duration ?? 4000)"
x-init="
    @if(session('success')) $nextTick(() => display('success', @js(session('success')))) @endif
    @if(session('error'))   $nextTick(() => display('error',   @js(session('error'))))   @endif
"
class="toast-notify-wrapper">

    <div x-show="show" x-cloak
         x-transition:enter="anim-toast-enter-to"
         x-transition:enter-start="anim-toast-enter"
         x-transition:leave="anim-toast-leave-to"
         x-transition:leave-end="anim-toast-leave-to"
         class="toast-notify"
         :class="type === 'success' ? 'toast-notify--success' : 'toast-notify--danger'">
        <div class="toast-notify__icon">
            <i :class="type === 'success' ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"></i>
        </div>
        <span class="toast-notify__message" x-text="message"></span>
        <button type="button" class="toast-notify__close" @click="show = false">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>
