<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoTripsSeeder extends Seeder
{
    /**
     * Crée ou met à jour deux voyages de démonstration pour le 21 octobre 2026.
     * Cette seeder ne supprime aucune donnée existante.
     */
    public function run(): void
    {
        $agency = Agency::query()->firstOrFail();
        $driver = Driver::query()->firstOrFail();

        // Dans les données de démonstration MonSiège, le premier véhicule
        // créé est normalement le Mercedes-Benz Sprinter.
        $vehicle = Vehicle::query()->orderBy('created_at')->firstOrFail();

        DB::transaction(function () use ($agency, $driver, $vehicle): void {
            Trip::updateOrCreate(
                [
                    'departure_city' => 'Antananarivo',
                    'arrival_city' => 'Toamasina',
                    'departure_at' => '2026-10-21 07:00:00',
                ],
                [
                    'agency_id' => $agency->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'departure_address' => 'Gare routière, Antananarivo',
                    'arrival_address' => 'Gare routière, Toamasina',
                    'arrival_at' => '2026-10-21 12:30:00',
                    'price' => 35000,
                    'currency' => 'MGA',
                    'status' => 'scheduled',
                    'notes' => 'Voyage de démonstration MonSiège.',
                ]
            );

            Trip::updateOrCreate(
                [
                    'departure_city' => 'Antananarivo',
                    'arrival_city' => 'Antsirabe',
                    'departure_at' => '2026-10-21 08:00:00',
                ],
                [
                    'agency_id' => $agency->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'departure_address' => 'Gare routière, Antananarivo',
                    'arrival_address' => 'Gare routière, Antsirabe',
                    'arrival_at' => '2026-10-21 12:30:00',
                    'price' => 25000,
                    'currency' => 'MGA',
                    'status' => 'scheduled',
                    'notes' => 'Voyage de démonstration MonSiège.',
                ]
            );
        });

        $this->command?->info('Les deux voyages du 21 octobre 2026 ont été créés ou mis à jour.');
    }
}
