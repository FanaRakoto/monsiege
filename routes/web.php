<?php

use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Accueil public
|--------------------------------------------------------------------------
*/

Route::get('/', [TripController::class, 'home'])->name('home');

/*
|--------------------------------------------------------------------------
| Voyages publics
|--------------------------------------------------------------------------
*/
Route::prefix('voyages')
    ->name('trips.')
    ->controller(TripController::class)
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('/', 'index')->name('index');

        Route::get('/{trip}', 'show')
            ->whereUuid('trip')
            ->name('show');

        Route::view('/historique', 'history.notice')
            ->middleware('throttle:30,1')
            ->name('history.notice');
    });

/*
|--------------------------------------------------------------------------
| Réservation et paiement de démonstration
|--------------------------------------------------------------------------
| Ce groupe accepte les visiteurs non connectés et les clients connectés.
| Les contrôleurs vérifient la propriété de la réservation avec l'utilisateur
| connecté ou l'identifiant conservé dans la session de réservation.
|--------------------------------------------------------------------------
*/
Route::prefix('voyages/{trip}/reservation')
    ->name('reservations.')
    ->group(function (): void {
        Route::get('/', [ReservationController::class, 'create'])
            ->whereUuid('trip')
            ->middleware('throttle:30,1')
            ->name('create');

        Route::post('/selection', [ReservationController::class, 'storeSelection'])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('selection');

        Route::get('/passagers', [ReservationController::class, 'passengers'])
            ->whereUuid('trip')
            ->middleware('throttle:30,1')
            ->name('passengers');

        Route::post('/passagers', [ReservationController::class, 'storePassengers'])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('passengers.store');

        Route::get('/paiement', [PaymentController::class, 'show'])
            ->whereUuid('trip')
            ->middleware('throttle:30,1')
            ->name('payment.show');

        Route::post('/paiement/simulation', [PaymentController::class, 'simulate'])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('payment.simulate');
    });

/*
|--------------------------------------------------------------------------
| Affichage du billet
|--------------------------------------------------------------------------
| Route publique au niveau du middleware seulement : TicketController vérifie
| que le compte connecté possède le billet ou que la session invitée contient
| l'identifiant exact du billet. Un UUID seul ne donne donc pas accès au billet.
|--------------------------------------------------------------------------
*/
Route::get('/billets/{ticket}', [TicketController::class, 'show'])
    ->whereUuid('ticket')
    ->name('tickets.show');

/*
|--------------------------------------------------------------------------
| Vérification et contrôle du QR Code : personnel connecté uniquement
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function (): void {
    Route::get('/verification-billet/{token}', [TicketController::class, 'verify'])
        ->whereUuid('token')
        ->name('tickets.verify');

    Route::post('/verification-billet/{token}', [TicketController::class, 'check'])
        ->whereUuid('token')
        ->middleware('throttle:20,1')
        ->name('tickets.check');

    Route::get('/client/dashboard', [DashboardController::class, 'index'])
        ->name('client.dashboard');

    Route::post('/deconnexion', [ClientAuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Connexion et inscription
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/connexion', [ClientAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/connexion', [ClientAuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.store');

    Route::get('/inscription', [ClientAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/inscription', [ClientAuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});


// Connexion d'administration distincte de la connexion client.
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/connexion', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/connexion', [AdminAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware('auth')->group(function (): void {
        Route::post('/deconnexion', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/voyages', [AdminDashboardController::class, 'trips'])->name('trips');
        Route::get('/reservations', [AdminDashboardController::class, 'reservations'])->name('reservations');
        Route::get('/utilisateurs', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/vehicules', [AdminDashboardController::class, 'vehicles'])->name('vehicles');
    });
});
