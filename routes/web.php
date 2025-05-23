<?php

use App\Livewire\Settings\Profile;
use App\Livewire\Tenants\Register;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Tenants\Identificator;
use Illuminate\Support\Facades\Auth;

// Routes publiques (sans tenant)
Route::get('/', function () {
    Auth::logout();
    return view('welcome');
})->name('home');

// Routes d'identification et d'enregistrement de tenant
Route::get('/identification', Identificator::class)->name('identification');
Route::get('/registration', Register::class)->name('registration');

// Routes spécifiques aux tenants avec le paramètre tenant dans l'URL
Route::prefix('tenant/{tenant}')->middleware(['tenant'])->group(function () {

    // Routes d'authentification spécifiques au tenant
    Route::get('/login', Login::class)->name('tenant.login');
    Route::post('/logout', function() {
        Auth::logout();
        return redirect()->route('home');
    })->name('tenant.logout');

    // Routes protégées par authentification
    Route::middleware(['auth'])->group(function () {

        // Dashboard et routes principales de l'application
        Route::view('/dashboard', 'dashboard')
            ->middleware(['verified'])
            ->name('tenant.dashboard');

        // Redirection vers le profil pour les paramètres
        Route::redirect('/settings', '/tenant/' . request()->route('tenant') . '/settings/profile');

        // Paramètres du profil et de l'application
        Route::get('/settings/profile', Profile::class)->name('tenant.settings.profile');
        Route::get('/settings/password', Password::class)->name('tenant.settings.password');
        Route::get('/settings/appearance', Appearance::class)->name('tenant.settings.appearance');

        // Routes pour les rôles et permissions
        Route::get('/settings/roles', App\Livewire\Settings\Roles\Index::class)
            ->name('tenant.settings.roles.index')
            ->middleware('can:manage-roles');
        Route::get('/settings/roles/create', App\Livewire\Settings\Roles\CreateEdit::class)
            ->name('tenant.settings.roles.create')
            ->middleware('can:manage-roles');
        Route::get('/settings/roles/{id}/edit', App\Livewire\Settings\Roles\CreateEdit::class)
            ->name('tenant.settings.roles.edit')
            ->middleware('can:manage-roles');
        Route::get('/settings/roles/{id}/permissions', App\Livewire\Settings\Roles\Permissions::class)
            ->name('tenant.settings.roles.permissions')
            ->middleware('can:manage-roles');

        // Routes pour la gestion des utilisateurs
        Route::get('/settings/users', App\Livewire\Settings\Users\Index::class)
            ->name('tenant.settings.users.index')
            ->middleware('can:manage-users');
        Route::get('/settings/users/create', App\Livewire\Settings\Users\CreateEdit::class)
            ->name('tenant.settings.users.create')
            ->middleware('can:manage-users');
        Route::get('/settings/users/{id}/edit', App\Livewire\Settings\Users\CreateEdit::class)
            ->name('tenant.settings.users.edit')
            ->middleware('can:manage-users');
        Route::get('/settings/users/{id}/show', App\Livewire\Settings\Users\Show::class)
            ->name('tenant.settings.users.show')
            ->middleware('can:manage-users');

        // Routes pour le module Catalogue
        Route::get('/catalog', App\Livewire\Catalog\Index::class)
            ->name('tenant.catalog.index')
            ->middleware('can:catalog.view');
        Route::get('/catalog/import-export', App\Livewire\Catalog\ImportExport::class)
            ->name('tenant.catalog.import-export')
            ->middleware('can:catalog.view');

        // Routes pour la gestion des emplacements/rayons
        Route::get('/catalog/aisles', App\Livewire\Catalog\Aisles\Index::class)
            ->name('tenant.catalog.aisles.index')
            ->middleware('can:catalog.view');
        Route::get('/catalog/aisles/create', App\Livewire\Catalog\Aisles\Create::class)
            ->name('tenant.catalog.aisles.create')
            ->middleware('can:catalog.create');
        Route::get('/catalog/aisles/{id}/edit', App\Livewire\Catalog\Aisles\Edit::class)
            ->name('tenant.catalog.aisles.edit')
            ->middleware('can:catalog.edit');


        Route::get('/catalog/products/create', App\Livewire\Catalog\Products\Create::class)
            ->name('tenant.catalog.products.create')
            ->middleware('can:catalog.create');
        Route::get('/catalog/products/{id}/edit', App\Livewire\Catalog\Products\Edit::class)
            ->name('tenant.catalog.products.edit')
            ->middleware('can:catalog.edit');
        Route::get('/catalog/products/{id}', App\Livewire\Catalog\Products\Show::class)
            ->name('tenant.catalog.products.show')
            ->middleware('can:catalog.view');

        // Routes pour la gestion des catégories
        Route::get('/catalog/categories', App\Livewire\Catalog\Categories\Index::class)
            ->name('tenant.catalog.categories.index')
            ->middleware('can:catalog.view');
        Route::get('/catalog/categories/create', App\Livewire\Catalog\Categories\Create::class)
            ->name('tenant.catalog.categories.create')
            ->middleware('can:catalog.create');
        Route::get('/catalog/categories/{id}/edit', App\Livewire\Catalog\Categories\Edit::class)
            ->name('tenant.catalog.categories.edit')
            ->middleware('can:catalog.edit');
    });
});

// Notes: Ces routes originales sont commentées pour référence et seront supprimées après migration complète
/*
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')
        ->middleware(['verified'])
        ->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});
*/

require __DIR__.'/auth.php';
