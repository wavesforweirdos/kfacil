<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PageController;
use App\Models\Page;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\PropertyOperationController;
use App\Http\Controllers\PropertyStatusController;
use App\Http\Controllers\PublicPropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $home = Page::where('type', 'home')->where('published', true)->first();

    return $home ? app(PageController::class)->show($home) : view('welcome');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $prefix = auth()->user()?->panelPrefix();

        return match ($prefix) {
            'admin' => redirect()->route('admin.dashboard'),
            'agente' => redirect()->route('agente.dashboard'),
            default => redirect('/'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| PROPERTY MANAGEMENT ROUTES (Permission-based)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::resource('property-types', PropertyTypeController::class)->except(['show']);
    Route::resource('property-operations', PropertyOperationController::class)->except(['show']);
    Route::resource('property-statuses', PropertyStatusController::class)->except(['show']);
});

/*
|--------------------------------------------------------------------------
| PAGE BUILDER ROUTES (Admin & Agents)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('pages/navbar/editor', [PageController::class, 'editNavbar'])->name('pages.navbar.edit');
    Route::put('pages/navbar/settings', [PageController::class, 'updateNavbar'])->name('pages.navbar.update');
    Route::resource('pages', PageController::class)->except(['show']);
    Route::put('pages/{page}/publish', [PageController::class, 'publish'])->name('pages.publish');
    Route::put('pages/{page}/unpublish', [PageController::class, 'unpublish'])->name('pages.unpublish');
});

// Formulario de contacto de la web pública y bandeja de mensajes del CRM
Route::post('/contacto', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::middleware('auth')->group(function () {
    Route::resource('messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
});

// Public properties (fixed property-detail structure)
Route::get('/inmuebles', [PublicPropertyController::class, 'index'])->name('public.properties.index');
Route::get('/inmuebles/{property}', [PublicPropertyController::class, 'show'])->name('public.properties.show');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('users', UserController::class);

        Route::resource('properties', PropertyController::class)->except(['create', 'edit']);
        Route::post('properties/{property}/clone', [PropertyController::class, 'clone'])->name('properties.clone');

    });


/*
|--------------------------------------------------------------------------
| AGENT ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:agente'])
    ->prefix('agente')
    ->name('agente.')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('properties', PropertyController::class)->except(['create', 'edit']);
        Route::post('properties/{property}/clone', [PropertyController::class, 'clone'])->name('properties.clone');

    });

require __DIR__ . '/auth.php';

// Public pages (catch-all should be last)
Route::get('/{page}', [PageController::class, 'show'])->name('pages.show');
