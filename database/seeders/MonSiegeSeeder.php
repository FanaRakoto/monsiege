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
     * Exécuter les données de démonstration MonSiège.
     */
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | 1. UTILISATEUR DE L'AGENCE
            |--------------------------------------------------------------------------
            */

            $agencyUser = User::create([
                'name' => 'MonSiège Transport',
                'email' => 'agence@monsiege.test',
                'phone' => '0320000000',
                'password' => 'password',
                'role' => 'agency',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2. AGENCE
            |--------------------------------------------------------------------------
            */

            $agency = Agency::create([
                'owner_user_id' => $agencyUser->id,
                'name' => 'MonSiège Transport',
                'code' => 'MST',
                'phone' => '0320000000',
                'email' => 'agence@monsiege.test',
                'address' => 'Analakely',
                'city' => 'Antananarivo',
                'description' => 'Agence de transport et de réservation de places à Madagascar.',
                'status' => 'active',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 3. UTILISATEUR CHAUFFEUR
            |--------------------------------------------------------------------------
            */

            $driverUser = User::create([
                'name' => 'Jean Rakoto',
                'email' => 'chauffeur@monsiege.test',
                'phone' => '0331000000',
                'password' => 'password',
                'role' => 'driver',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 4. CHAUFFEUR
            |--------------------------------------------------------------------------
            */

            $driver = Driver::create([
                'user_id' => $driverUser->id,
                'agency_id' => $agency->id,
                'license_number' => 'MAD-DL-00001',
                'license_expiry_date' => now()->addYears(2),
                'status' => 'active',
                'notes' => 'Chauffeur de démonstration pour les tests MonSiège.',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 5. PREMIER VÉHICULE : MERCEDES-BENZ SPRINTER
            |--------------------------------------------------------------------------
            |
            | Configuration :
            |
            |                  AVANT
            |
            |              [ CHAUFFEUR ]
            |                  🚗
            |
            |               A1      A2
            |               💺      💺
            |
            |             01 02   03 04
            |             05 06   07 08
            |             09 10   11 12
            |             13 14   15 16
            |
            |                  ARRIÈRE
            |
            | Chauffeur : non disponible
            | Passagers : 18 sièges
            |
            */

            $vehicle1 = Vehicle::create([
                'agency_id' => $agency->id,
                'registration_number' => '1234 TAA',
                'brand' => 'Mercedes-Benz',
                'model' => 'Sprinter',
                'type' => 'minibus',
                'capacity' => 18,
                'status' => 'active',
                'description' => 'Mercedes-Benz Sprinter confortable pour les trajets interurbains.',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 6. DEUX SIÈGES PASSAGERS À L'AVANT
            |--------------------------------------------------------------------------
            */

            Seat::create([
                'vehicle_id' => $vehicle1->id,
                'seat_number' => 'A1',
                'row_number' => 0,
                'column_number' => 1,
                'seat_type' => 'standard',
                'is_window' => true,
                'is_active' => true,
            ]);

            Seat::create([
                'vehicle_id' => $vehicle1->id,
                'seat_number' => 'A2',
                'row_number' => 0,
                'column_number' => 2,
                'seat_type' => 'standard',
                'is_window' => true,
                'is_active' => true,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 7. QUATRE RANGÉES DE QUATRE SIÈGES
            |--------------------------------------------------------------------------
            |
            | Rangée 1 : 01 02 | 03 04
            | Rangée 2 : 05 06 | 07 08
            | Rangée 3 : 09 10 | 11 12
            | Rangée 4 : 13 14 | 15 16
            |
            */

            $seatNumber = 1;

            for ($row = 1; $row <= 4; $row++) {

                for ($column = 1; $column <= 4; $column++) {

                    Seat::create([
                        'vehicle_id' => $vehicle1->id,
                        'seat_number' => str_pad(
                            (string) $seatNumber,
                            2,
                            '0',
                            STR_PAD_LEFT
                        ),
                        'row_number' => $row,
                        'column_number' => $column,
                        'seat_type' => 'standard',
                        'is_window' => in_array($column, [1, 4], true),
                        'is_active' => true,
                    ]);

                    $seatNumber++;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | 8. DEUXIÈME VÉHICULE : TOYOTA COASTER
            |--------------------------------------------------------------------------
            |
            | 20 sièges :
            | 5 rangées × 4 sièges
            |
            */

            $vehicle2 = Vehicle::create([
                'agency_id' => $agency->id,
                'registration_number' => '5678 TBB',
                'brand' => 'Toyota',
                'model' => 'Coaster',
                'type' => 'bus',
                'capacity' => 20,
                'status' => 'active',
                'description' => 'Toyota Coaster destiné aux trajets longue distance.',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 9. SIÈGES DU TOYOTA COASTER
            |--------------------------------------------------------------------------
            */

            $seatNumber = 1;

            for ($row = 1; $row <= 5; $row++) {

                for ($column = 1; $column <= 4; $column++) {

                    Seat::create([
                        'vehicle_id' => $vehicle2->id,
                        'seat_number' => str_pad(
                            (string) $seatNumber,
                            2,
                            '0',
                            STR_PAD_LEFT
                        ),
                        'row_number' => $row,
                        'column_number' => $column,
                        'seat_type' => 'standard',
                        'is_window' => in_array($column, [1, 4], true),
                        'is_active' => true,
                    ]);

                    $seatNumber++;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | 10. VOYAGE : ANTANANARIVO → TOAMASINA
            |--------------------------------------------------------------------------
            */

            $tripToamasina = Trip::create([
                'agency_id' => $agency->id,
                'vehicle_id' => $vehicle1->id,
                'driver_id' => $driver->id,

                'departure_city' => 'Antananarivo',
                'arrival_city' => 'Toamasina',

                'departure_address' => 'Gare routière, Antananarivo',
                'arrival_address' => 'Gare routière, Toamasina',

                'departure_at' => now()
                    ->addDay()
                    ->setTime(7, 0),

                'arrival_at' => now()
                    ->addDay()
                    ->setTime(12, 30),

                'price' => 35000,
                'currency' => 'MGA',

                'status' => 'scheduled',

                'notes' => 'Départ à 07h00. Merci d’arriver 30 minutes avant.',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 11. VOYAGE : ANTANANARIVO → ANTSIRABE
            |--------------------------------------------------------------------------
            */

            $tripAntsirabe = Trip::create([
                'agency_id' => $agency->id,
                'vehicle_id' => $vehicle1->id,
                'driver_id' => $driver->id,

                'departure_city' => 'Antananarivo',
                'arrival_city' => 'Antsirabe',

                'departure_address' => 'Gare routière, Antananarivo',
                'arrival_address' => 'Gare routière, Antsirabe',

                'departure_at' => now()
                    ->addDays(2)
                    ->setTime(8, 0),

                'arrival_at' => now()
                    ->addDays(2)
                    ->setTime(12, 30),

                'price' => 25000,
                'currency' => 'MGA',

                'status' => 'scheduled',

                'notes' => 'Trajet direct Antananarivo → Antsirabe.',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 12. VOYAGE : ANTANANARIVO → MAHAJANGA
            |--------------------------------------------------------------------------
            */

            $tripMahajanga = Trip::create([
                'agency_id' => $agency->id,
                'vehicle_id' => $vehicle2->id,
                'driver_id' => $driver->id,

                'departure_city' => 'Antananarivo',
                'arrival_city' => 'Mahajanga',

                'departure_address' => 'Gare routière, Antananarivo',
                'arrival_address' => 'Gare routière, Mahajanga',

                'departure_at' => now()
                    ->addDays(3)
                    ->setTime(6, 30),

                'arrival_at' => now()
                    ->addDays(3)
                    ->setTime(14, 30),

                'price' => 30000,
                'currency' => 'MGA',

                'status' => 'scheduled',

                'notes' => 'Trajet longue distance.',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 13. UTILISATEUR CLIENT / RÉSERVATEUR
            |--------------------------------------------------------------------------
            */

            $clientUser = User::create([
                'name' => 'Fanantenana',
                'email' => 'client@monsiege.test',
                'phone' => '0341000000',
                'password' => 'password',
                'role' => 'client',
            ]);


            /*
            |--------------------------------------------------------------------------
            | 14. INFORMATIONS DANS LA CONSOLE
            |--------------------------------------------------------------------------
            */

            $this->command->newLine();

            $this->command->info('===========================================');
            $this->command->info('     MONSIÈGE - SEEDER TERMINÉ');
            $this->command->info('===========================================');

            $this->command->info('');
            $this->command->info('UTILISATEURS');
            $this->command->info('Agence    : ' . $agencyUser->email);
            $this->command->info('Chauffeur : ' . $driverUser->email);
            $this->command->info('Client    : ' . $clientUser->email);
            $this->command->info('Mot de passe : password');

            $this->command->info('');

            $this->command->info('AGENCE');
            $this->command->info(
                $agency->name . ' [' . $agency->code . ']'
            );

            $this->command->info('');

            $this->command->info('VÉHICULES');
            $this->command->info(
                'Sprinter : ' . $vehicle1->registration_number
                    . ' - 18 sièges'
            );

            $this->command->info(
                'Coaster  : ' . $vehicle2->registration_number
                    . ' - 20 sièges'
            );

            $this->command->info('');

            $this->command->info('SIÈGES CRÉÉS');
            $this->command->info(
                'Sprinter : 18'
            );

            $this->command->info(
                'Coaster  : 20'
            );

            $this->command->info(
                'Total     : 38'
            );

            $this->command->info('');

            $this->command->info('VOYAGES');
            $this->command->info(
                'Antananarivo → Toamasina : 35 000 Ar'
            );

            $this->command->info(
                'Antananarivo → Antsirabe : 25 000 Ar'
            );

            $this->command->info(
                'Antananarivo → Mahajanga : 30 000 Ar'
            );

            $this->command->info('');

            $this->command->info('===========================================');
            $this->command->info('MonSiège est prêt pour les tests.');
            $this->command->info('===========================================');

            $this->command->newLine();
        });
    }
}
