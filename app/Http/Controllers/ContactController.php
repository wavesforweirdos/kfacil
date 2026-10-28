<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Guarda el mensaje del formulario público de contacto.
     * El campo "website" es una trampa para bots: si viene relleno se descarta en silencio.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'string'],
        ]);

        if (! blank($data['website'] ?? null)) {
            return back()->with('contact_success', 'Mensaje enviado. Te responderemos lo antes posible.');
        }

        ContactMessage::create(collect($data)->except('website')->all());

        return back()->with('contact_success', 'Mensaje enviado. Te responderemos lo antes posible.');
    }
}
