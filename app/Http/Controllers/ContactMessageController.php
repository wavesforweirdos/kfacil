<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mensajes recibidos desde el formulario de contacto. Los ven admins y agentes;
 * solo los admins los borran.
 */
class ContactMessageController extends Controller
{
    private function authorizeStaff(Request $request): void
    {
        $user = $request->user();

        abort_unless($user && ($user->isAdmin() || $user->isAgent()), 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeStaff($request);

        $messages = ContactMessage::query()
            ->when($request->query('estado') === 'sin-leer', fn ($q) => $q->unread())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('messages.index', compact('messages'));
    }

    public function show(Request $request, ContactMessage $message): View
    {
        $this->authorizeStaff($request);

        $message->markAsRead();

        return view('messages.show', compact('message'));
    }

    public function destroy(Request $request, ContactMessage $message): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Mensaje eliminado.');
    }
}
