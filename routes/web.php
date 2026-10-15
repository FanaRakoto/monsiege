
<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DashboardController;

/*
|--------------------------------------------------------------------------
| Accueil public
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')
    ->name('home');


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

Route::prefix('voyages/{trip}/reservation')
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
    });

