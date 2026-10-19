
<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;

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
|
| Préfixe URI : /voyages
| Préfixe nom : trips.
| Middleware  : web + limitation de débit
|
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
| Réservation des sièges
|--------------------------------------------------------------------------
|
| Le parcours autorise actuellement les visiteurs non connectés.
| La réservation est associée à la session du visiteur.
|
| Les contrôleurs doivent toujours vérifier les sièges côté serveur.
|
*/


/*
|--------------------------------------------------------------------------
| Réservations — clients connectés uniquement
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('voyages/{trip}/reservation')
    ->name('reservations.')
    ->group(function (): void {

        // Afficher le plan des sièges
        Route::get('/', [ReservationController::class, 'create'])
            ->whereUuid('trip')
            ->middleware('throttle:30,1')
            ->name('create');

        // Vérifier la sélection et verrouiller les sièges
        Route::post('/selection', [ReservationController::class, 'storeSelection'])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('selection');

        // Afficher le formulaire des passagers
        Route::get('/passagers', [ReservationController::class, 'passengers'])
            ->whereUuid('trip')
            ->middleware('throttle:30,1')
            ->name('passengers');

        // Enregistrer les passagers
        Route::post('/passagers', [
            ReservationController::class,
            'storePassengers',
        ])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('passengers.store');

        // Afficher le paiement simulé
        Route::get('/paiement', [
            PaymentController::class,
            'show',
        ])
            ->whereUuid('trip')
            ->name('payment.show');

        // Exécuter une simulation de paiement (développement uniquement)
        Route::post('/paiement/simulation', [
            PaymentController::class,
            'simulate',
        ])
            ->whereUuid('trip')
            ->middleware('throttle:10,1')
            ->name('payment.simulate');


        // Billets et validation par QR Code : utilisateurs connectés uniquement.
        Route::middleware('auth')->group(function (): void {
            Route::get('/billets/{ticket}', [
                TicketController::class,
                'show',
            ])
                ->whereUuid('ticket')
                ->name('tickets.show');

            Route::get('/verification-billet/{token}', [
                TicketController::class,
                'verify',
            ])
                ->whereUuid('token')
                ->name('tickets.verify');

            Route::post('/verification-billet/{token}', [
                TicketController::class,
                'check',
            ])
                ->whereUuid('token')
                ->middleware('throttle:20,1')
                ->name('tickets.check');
        });
    });



Route::middleware('auth')->group(function () {
    Route::get('/client/dashboard', [
        DashboardController::class,
        'index',
    ])->name('client.dashboard');
});
Route::post('/deconnexion', [ClientAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [ClientAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/connexion', [ClientAuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::get('/inscription', [ClientAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/inscription', [ClientAuthController::class, 'register'])
        ->middleware('throttle:5,1');
});
