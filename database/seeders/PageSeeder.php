<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role_id', 1)->first();

        if (! $admin) {
            $admin = User::first();
        }

        if (! $admin || Page::exists()) {
            return;
        }

        // Home Page
        $homePage = Page::create([
            'slug' => 'inicio',
            'title' => 'Inicio',
            'type' => 'home',
            'description' => 'Página de inicio de K Fácil Inmobiliaria',
            'meta_title' => 'Inicio - K Fácil Inmobiliaria',
            'meta_description' => 'Encuentra las mejores propiedades inmobiliarias en nuestro portal',
            'meta_keywords' => 'inmuebles, propiedades, venta, alquiler',
            'editable_by_agents' => false,
            'published' => true,
            'created_by' => $admin->id,
        ]);

        // Hero block for home
        PageBlock::create([
            'page_id' => $homePage->id,
            'component' => 'hero',
            'order' => 0,
            'data' => [
                'title' => 'Bienvenido a K Fácil Inmobiliaria',
                'subtitle' => 'Encuentra las mejores propiedades inmobiliarias con nosotros',
                'cta_text' => 'Explorar propiedades',
                'cta_url' => '/propiedades',
                'height' => 'lg',
            ],
        ]);

        // Properties Featured block
        PageBlock::create([
            'page_id' => $homePage->id,
            'component' => 'properties_featured',
            'order' => 1,
            'data' => [
                'title' => 'Propiedades Destacadas',
                'limit' => 6,
                'columns' => 3,
            ],
        ]);

        // CTA block
        PageBlock::create([
            'page_id' => $homePage->id,
            'component' => 'cta',
            'order' => 2,
            'data' => [
                'title' => '¿Tienes una propiedad que vender o alquilar?',
                'description' => 'Contacta con nuestro equipo de expertos para una asesoría personalizada',
                'button_text' => 'Contactar ahora',
                'button_url' => '/contacto',
                'background_color' => '#2e6fa3',
            ],
        ]);

        // Services Page
        $servicesPage = Page::create([
            'slug' => 'servicios',
            'title' => 'Servicios',
            'type' => 'servicios',
            'description' => 'Descubre los servicios que ofrecemos',
            'meta_title' => 'Servicios - K Fácil Inmobiliaria',
            'meta_description' => 'Conoce todos los servicios inmobiliarios que ofrecemos',
            'editable_by_agents' => false,
            'published' => true,
            'created_by' => $admin->id,
        ]);

        PageBlock::create([
            'page_id' => $servicesPage->id,
            'component' => 'hero',
            'order' => 0,
            'data' => [
                'title' => 'Nuestros Servicios',
                'subtitle' => 'Soluciones inmobiliarias integrales',
                'height' => 'md',
            ],
        ]);

        PageBlock::create([
            'page_id' => $servicesPage->id,
            'component' => 'services',
            'order' => 1,
            'data' => [
                'title' => 'Servicios Principales',
                'columns' => 3,
                'services' => [
                    [
                        'icon' => '🏠',
                        'title' => 'Venta de propiedades',
                        'description' => 'Asesoramiento completo para vender tu propiedad al mejor precio',
                        'url' => '/propiedades',
                    ],
                    [
                        'icon' => '🔑',
                        'title' => 'Alquiler de propiedades',
                        'description' => 'Encuentra el inmueble perfecto en alquiler para tus necesidades',
                        'url' => '/propiedades',
                    ],
                    [
                        'icon' => '💼',
                        'title' => 'Asesoramiento legal',
                        'description' => 'Asesoramiento profesional en todos tus trámites inmobiliarios',
                        'url' => '/contacto',
                    ],
                ],
            ],
        ]);

        // Contact Page
        $contactPage = Page::create([
            'slug' => 'contacto',
            'title' => 'Contacto',
            'type' => 'contacto',
            'description' => 'Ponte en contacto con nosotros',
            'meta_title' => 'Contacto - K Fácil Inmobiliaria',
            'meta_description' => 'Contáctanos para más información sobre nuestros servicios',
            'editable_by_agents' => false,
            'published' => true,
            'created_by' => $admin->id,
        ]);

        PageBlock::create([
            'page_id' => $contactPage->id,
            'component' => 'hero',
            'order' => 0,
            'data' => [
                'title' => 'Contacto',
                'subtitle' => 'Nos encantaría hablar contigo',
                'height' => 'md',
            ],
        ]);

        PageBlock::create([
            'page_id' => $contactPage->id,
            'component' => 'contact_form',
            'order' => 1,
            'data' => [
                'title' => 'Formulario de Contacto',
                'description' => 'Rellena el formulario y nos pondremos en contacto contigo lo antes posible',
                'show_map' => false,
            ],
        ]);

        // Properties Page
        $propertiesPage = Page::create([
            'slug' => 'propiedades',
            'title' => 'Propiedades',
            'type' => 'propiedades',
            'description' => 'Todas nuestras propiedades disponibles',
            'meta_title' => 'Propiedades - K Fácil Inmobiliaria',
            'meta_description' => 'Busca entre cientos de propiedades disponibles',
            'editable_by_agents' => false,
            'published' => true,
            'created_by' => $admin->id,
        ]);

        PageBlock::create([
            'page_id' => $propertiesPage->id,
            'component' => 'hero',
            'order' => 0,
            'data' => [
                'title' => 'Propiedades Disponibles',
                'subtitle' => 'Encuentra el inmueble de tus sueños',
                'height' => 'md',
            ],
        ]);

        PageBlock::create([
            'page_id' => $propertiesPage->id,
            'component' => 'properties_search',
            'order' => 1,
            'data' => [
                'title' => 'Buscar propiedades',
                'view_type' => 'grid',
                'items_per_page' => 12,
            ],
        ]);
    }
}
