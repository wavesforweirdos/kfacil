<section class="py-20 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        @if($block->data['title'] ?? null)
            <div class="text-center mb-4">
                <h2 class="text-4xl font-bold text-gray-900 font-playfair">
                    {{ $block->data['title'] }}
                </h2>
            </div>
        @endif

        @if($block->data['description'] ?? null)
            <div class="text-center mb-12">
                <p class="text-lg text-gray-600">
                    {{ $block->data['description'] }}
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="text-center">
                <div class="text-3xl text-blue-600 mb-4">
                    <i class="fas fa-phone"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Teléfono</h3>
                <p class="text-gray-600">{{ config('app.phone', '+34 123 456 789') }}</p>
            </div>

            <div class="text-center">
                <div class="text-3xl text-blue-600 mb-4">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Email</h3>
                <p class="text-gray-600">{{ config('app.email', 'info@kfacil.com') }}</p>
            </div>

            <div class="text-center">
                <div class="text-3xl text-blue-600 mb-4">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Ubicación</h3>
                <p class="text-gray-600">{{ config('app.address', 'Dirección') }}</p>
            </div>
        </div>

        @if($block->data['show_map'] ?? false)
            <div class="mb-12">
                <iframe 
                    class="w-full h-96 rounded-lg"
                    src="https://www.google.com/maps/embed?pb=..." 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy"
                ></iframe>
            </div>
        @endif

        <form action="{{ route('contact.store') }}" method="POST" class="bg-white p-8 rounded-lg shadow-lg">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input 
                        type="text" 
                        name="name" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input 
                    type="tel" 
                    name="phone"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Asunto</label>
                <input 
                    type="text" 
                    name="subject" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                <textarea 
                    name="message" 
                    rows="5" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
            </div>

            <button 
                type="submit"
                class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition"
            >
                Enviar mensaje
            </button>
        </form>
    </div>
</section>
