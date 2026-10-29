<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

//Dans app/Providers/AppServiceProvider.php, conserver les imports et le code existants. Ajouter ces imports en haut :

use App\Models\Driver;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Observers\PlatformActivityObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /** 
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        User::observe(PlatformActivityObserver::class);
        Trip::observe(PlatformActivityObserver::class);
        Vehicle::observe(PlatformActivityObserver::class);
        Driver::observe(PlatformActivityObserver::class);
        Reservation::observe(PlatformActivityObserver::class);
        Payment::observe(PlatformActivityObserver::class);
        Ticket::observe(PlatformActivityObserver::class);
    }
}
