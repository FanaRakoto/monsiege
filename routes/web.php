<?php

use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accueil public
|--------------------------------------------------------------------------
*/

Route::get('/', [TripController::class, 'home'])
    ->name('home');

/*
|--------------------------------------------------------------------------
| Voyages publics
|--------------------------------------------------------------------------
| URL : /voyages
| Noms : trips.index, trips.show
|--------------------------------------------------------------------------
*/

Route::prefix('voyages')
    ->name('trips.')
    ->controller(TripController::class)
    ->middleware('throttle:60,1')
    ->group(function (): void {

        // Liste et recherche des voyages
        Route::get('/', 'index')
            ->name('index');

        // Détails d'un voyage identifié par UUID
        Route::get('/{trip}', 'show')
            ->whereUuid('trip')
            ->name('show');
    });

/*
|--------------------------------------------------------------------------
| Réservations et paiement
|--------------------------------------------------------------------------
| Ces routes nécessitent une connexion.
| URL de base : /voyages/{trip}/reservation
| Noms de base : reservations.*
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('voyages/{trip}/reservation')
    ->name('reservations.')
    ->group(function (): void {

        // Afficher le plan des sièges
        Route::get('/', [ReservationController::class, 'create'])
            ->whereUuid('trip')
            ->middleware('throttle:30,1')
            ->name('create');

        // Vérifier la sélection et verrouiller les sièges
        Route::post('/selection', [
            ReservationController::class,
            'storeSelection',
        ])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('selection');

        // Afficher le formulaire des passagers
        Route::get('/passagers', [
            ReservationController::class,
            'passengers',
        ])
            ->whereUuid('trip')
            ->middleware('throttle:30,1')
            ->name('passengers');

        // Enregistrer les informations des passagers
        Route::post('/passagers', [
            ReservationController::class,
            'storePassengers',
        ])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('passengers.store');

        // Afficher la page de paiement simulé
        Route::get('/paiement', [
            PaymentController::class,
            'show',
        ])
            ->whereUuid('trip')
            ->name('payment.show');

        // Simuler un paiement réussi ou échoué
        Route::post('/paiement/simulation', [
            PaymentController::class,
            'simulate',
        ])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('payment.simulate');
    });

/*
|--------------------------------------------------------------------------
| Billets et vérification par QR Code
|--------------------------------------------------------------------------
| IMPORTANT : ce groupe est indépendant du groupe de réservation.
| Les billets ont leur propre URL et leurs propres noms de routes.
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {

    // Afficher le billet du client propriétaire
    Route::get('/billets/{ticket}', [
        TicketController::class,
        'show',
    ])
        ->whereUuid('ticket')
        ->name('tickets.show');

    // Afficher les informations de vérification du billet
    Route::get('/verification-billet/{token}', [
        TicketController::class,
        'verify',
    ])
        ->whereUuid('token')
        ->name('tickets.verify');

    // Confirmer le contrôle et marquer le billet comme utilisé
    Route::post('/verification-billet/{token}', [
        TicketController::class,
        'check',
    ])
        ->whereUuid('token')
        ->middleware('throttle:20,1')
        ->name('tickets.check');

    // Tableau de bord client
    Route::get('/client/dashboard', [
        DashboardController::class,
        'index',
    ])
        ->name('client.dashboard');

    // Déconnexion
    Route::post('/deconnexion', [
        ClientAuthController::class,
        'logout',
    ])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Connexion et inscription
|--------------------------------------------------------------------------
| Routes accessibles aux visiteurs non connectés.
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {

    // Formulaire de connexion
    Route::get('/connexion', [
        ClientAuthController::class,
        'showLogin',
    ])
        ->name('login');

    // Traitement de la connexion
    Route::post('/connexion', [
        ClientAuthController::class,
        'login',
    ])
        ->middleware('throttle:10,1')
        ->name('login.store');

    // Formulaire d'inscription
    Route::get('/inscription', [
        ClientAuthController::class,
        'showRegister',
    ])
        ->name('register');

    // Traitement de l'inscription
    Route::post('/inscription', [
        ClientAuthController::class,
        'register',
    ])
        ->middleware('throttle:5,1')
        ->name('register.store');
});
