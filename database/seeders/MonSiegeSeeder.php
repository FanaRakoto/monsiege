<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Driver;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonSiegeSeeder extends Seeder
{
    /**
     * Prépare des données de démonstration pour le site public MonSiège.
     *
     * Ce seeder utilise updateOrCreate pour éviter de recréer les mêmes
     * utilisateurs, véhicules, sièges et voyages à chaque exécution.
     * À utiliser uniquement avec les données de démonstration, avant toute
     * réservation réelle : les horaires des trois voyages de démo sont mis à jour.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $agencyUser = User::updateOrCreate(
                ['email' => 'agence@monsiege.test'],
                [
                    'name' => 'MonSiège Transport',
                    'phone' => '0320000000',
                    'password' => 'password',
                    'role' => 'agency',
                ]
            );

            $agency = Agency::updateOrCreate(
                ['code' => 'MST'],
                [
                    'owner_user_id' => $agencyUser->id,
                    'name' => 'MonSiège Transport',
                    'phone' => '0320000000',
                    'email' => 'agence@monsiege.test',
                    'address' => 'Analakely',
                    'city' => 'Antananarivo',
                    'description' => 'Agence de démonstration pour MonSiège.',
                    'status' => 'active',
                ]
            );

            $driverUser = User::updateOrCreate(
                ['email' => 'chauffeur@monsiege.test'],
                [
                    'name' => 'Jean Rakoto',
                    'phone' => '0331000000',
                    'password' => 'password',
                    'role' => 'driver',
                ]
            );

            $driver = Driver::updateOrCreate(
                ['user_id' => $driverUser->id],
                [
                    'agency_id' => $agency->id,
                    'license_number' => 'MAD-DL-00001',
                    'license_expiry_date' => now()->addYears(2)->toDateString(),
                    'status' => 'active',
                    'notes' => 'Chauffeur de démonstration pour les tests MonSiège.',
                ]
            );

            $sprinter = Vehicle::updateOrCreate(
                ['registration_number' => '1234 TAA'],
                [
                    'agency_id' => $agency->id,
                    'brand' => 'Mercedes-Benz',
                    'model' => 'Sprinter',
                    'type' => 'minibus',
                    'capacity' => 18,
                    'status' => 'active',
                    'description' => 'Mercedes-Benz Sprinter pour les trajets interurbains.',
                ]
            );

            // 2 places à l'avant, puis 4 rangées de 4 places (18 au total).
            $frontSeats = [
                ['seat_number' => 'A1', 'row_number' => 0, 'column_number' => 1],
                ['seat_number' => 'A2', 'row_number' => 0, 'column_number' => 2],
            ];

            foreach ($frontSeats as $seatData) {
                Seat::updateOrCreate(
                    [
                        'vehicle_id' => $sprinter->id,
                        'seat_number' => $seatData['seat_number'],
                    ],
                    $seatData + [
                        'seat_type' => 'standard',
                        'is_window' => true,
                        'is_active' => true,
                    ]
                );
            }

            $number = 1;
            for ($row = 1; $row <= 4; $row++) {
                for ($column = 1; $column <= 4; $column++) {
                    Seat::updateOrCreate(
                        [
                            'vehicle_id' => $sprinter->id,
                            'seat_number' => str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                        ],
                        [
                            'row_number' => $row,
                            'column_number' => $column,
                            'seat_type' => 'standard',
                            'is_window' => in_array($column, [1, 4], true),
                            'is_active' => true,
                        ]
                    );
                    $number++;
                }
            }

            $coaster = Vehicle::updateOrCreate(
                ['registration_number' => '5678 TBB'],
                [
                    'agency_id' => $agency->id,
                    'brand' => 'Toyota',
                    'model' => 'Coaster',
                    'type' => 'bus',
                    'capacity' => 20,
                    'status' => 'active',
                    'description' => 'Toyota Coaster pour les trajets longue distance.',
                ]
            );

            $number = 1;
            for ($row = 1; $row <= 5; $row++) {
                for ($column = 1; $column <= 4; $column++) {
                    Seat::updateOrCreate(
                        [
                            'vehicle_id' => $coaster->id,
                            'seat_number' => str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                        ],
                        [
                            'row_number' => $row,
                            'column_number' => $column,
                            'seat_type' => 'standard',
                            'is_window' => in_array($column, [1, 4], true),
                            'is_active' => true,
                        ]
                    );
                    $number++;
                }
            }

            // Une seule fiche de démonstration par itinéraire.
            // Les dates sont placées dans le futur à chaque exécution.
            $demoTrips = [
                [
                    'departure_city' => 'Antananarivo',
                    'arrival_city' => 'Toamasina',
                    'vehicle_id' => $sprinter->id,
                    'departure_day_offset' => 1,
                    'departure_time' => [7, 0],
                    'arrival_time' => [12, 30],
                    'price' => 35000,
                    'image_path' => 'images/voyages/toamasina.jpeg',
                    'notes' => 'Départ à 07h00. Merci d’arriver 30 minutes avant.',
                ],
                [
                    'departure_city' => 'Antananarivo',
                    'arrival_city' => 'Fianarantsoa',
                    'vehicle_id' => $coaster->id,
                    'departure_day_offset' => 3,
                    'departure_time' => [6, 0],
                    'arrival_time' => [14, 0],
                    'price' => 50000,
                    'image_path' => 'images/voyages/fianarantsoa.jpeg',
                    'notes' => 'Voyage de démonstration vers les Hautes Terres.',
                ],
                [
                    'departure_city' => 'Antananarivo',
                    'arrival_city' => 'Toliara',
                    'vehicle_id' => $coaster->id,
                    'departure_day_offset' => 5,
                    'departure_time' => [5, 0],
                    'arrival_time' => [21, 0],
                    'price' => 85000,
                    'image_path' => 'images/voyages/toliara-rn7.jpeg',
                    'notes' => 'Trajet de démonstration par la RN7. Horaires et tarif à confirmer par l’agence.',
                ],
            ];

            foreach ($demoTrips as $data) {
                $departureAt = now()->startOfDay()
                    ->addDays($data['departure_day_offset'])
                    ->setTime($data['departure_time'][0], $data['departure_time'][1]);

                $arrivalAt = now()->startOfDay()
                    ->addDays($data['departure_day_offset'])
                    ->setTime($data['arrival_time'][0], $data['arrival_time'][1]);

                Trip::updateOrCreate(
                    [
                        'departure_city' => $data['departure_city'],
                        'arrival_city' => $data['arrival_city'],
                    ],
                    [
                        'agency_id' => $agency->id,
                        'vehicle_id' => $data['vehicle_id'],
                        'driver_id' => $driver->id,
                        'departure_address' => 'Gare routière, Antananarivo',
                        'arrival_address' => 'Gare routière, ' . $data['arrival_city'],
                        'departure_at' => $departureAt,
                        'arrival_at' => $arrivalAt,
                        'image_path' => $data['image_path'],
                        'price' => $data['price'],
                        'currency' => 'MGA',
                        'status' => 'scheduled',
                        'notes' => $data['notes'],
                    ]
                );
            }

            User::updateOrCreate(
                ['email' => 'client@monsiege.test'],
                [
                    'name' => 'Fanantenana',
                    'phone' => '0341000000',
                    'password' => 'password',
                    'role' => 'client',
                ]
            );

            $this->command?->newLine();
            $this->command?->info('MonSiège : données de démonstration prêtes.');
            $this->command?->info('Agence : agence@monsiege.test / password');
            $this->command?->info('Chauffeur : chauffeur@monsiege.test / password');
            $this->command?->info('Client : client@monsiege.test / password');
            $this->command?->info('Voyages : Toamasina, Fianarantsoa et Toliara (RN7).');
            $this->command?->warn('Comptes et tarifs réservés à la démonstration locale.');
        });
    }
}