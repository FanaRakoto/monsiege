<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoThirtyTripsSeeder extends Seeder
{
    /**
     * Crée 30 voyages de démonstration avec des dates/heures reproductibles.
     * Les données déjà présentes ne sont jamais supprimées.
     * Les tarifs et durées sont indicatifs, destinés aux tests locaux.
     */
    public function run(): void
    {
        $agency = Agency::query()->orderBy('created_at')->first();
        if (!$agency) {
            throw new RuntimeException('Aucune agence trouvée. Lance d’abord le seeder de base MonSiège.');
        }

        $vehicles = Vehicle::query()
            ->where('agency_id', $agency->id)
            ->where('status', 'active')
            ->orderBy('created_at')
            ->get();

        if ($vehicles->isEmpty()) {
            $vehicles = Vehicle::query()->where('agency_id', $agency->id)->orderBy('created_at')->get();
        }
        if ($vehicles->isEmpty()) {
            throw new RuntimeException('Aucun véhicule trouvé pour cette agence. Ajoute un véhicule avant de lancer ce seeder.');
        }

        $drivers = Driver::query()
            ->where('agency_id', $agency->id)
            ->where('status', 'active')
            ->orderBy('created_at')
            ->get();

        // Les trajets couvrent les six capitales provinciales. Les images et prix
        // sont des données de démonstration et peuvent être ajustés ensuite.
        $routes = [
            ['from' => 'Antananarivo', 'to' => 'Toamasina', 'price' => 35000, 'duration' => 330, 'image' => 'images/voyages/toamasina.jpeg'],
            ['from' => 'Toamasina', 'to' => 'Antananarivo', 'price' => 35000, 'duration' => 330, 'image' => 'images/voyages/toamasina.jpeg'],
            ['from' => 'Antananarivo', 'to' => 'Mahajanga', 'price' => 30000, 'duration' => 480, 'image' => 'images/voyages/mahajanga.jpeg'],
            ['from' => 'Mahajanga', 'to' => 'Antananarivo', 'price' => 30000, 'duration' => 480, 'image' => 'images/voyages/mahajanga.jpeg'],
            ['from' => 'Antananarivo', 'to' => 'Fianarantsoa', 'price' => 50000, 'duration' => 480, 'image' => 'images/voyages/fianarantsoa.jpeg'],
            ['from' => 'Fianarantsoa', 'to' => 'Antananarivo', 'price' => 50000, 'duration' => 480, 'image' => 'images/voyages/fianarantsoa.jpeg'],
            ['from' => 'Antananarivo', 'to' => 'Toliara', 'price' => 85000, 'duration' => 900, 'image' => 'images/voyages/toliara-rn7.jpeg'],
            ['from' => 'Toliara', 'to' => 'Antananarivo', 'price' => 85000, 'duration' => 900, 'image' => 'images/voyages/toliara-rn7.jpeg'],
            ['from' => 'Antananarivo', 'to' => 'Antsiranana', 'price' => 95000, 'duration' => 1080, 'image' => 'images/voyages/antsiranana.jpeg'],
            ['from' => 'Antsiranana', 'to' => 'Antananarivo', 'price' => 95000, 'duration' => 1080, 'image' => 'images/voyages/antsiranana.jpeg'],
        ];

        // Vérifier les images avant d'insérer une seule ligne.
        foreach ($routes as $route) {
            if (!is_file(public_path($route['image']))) {
                throw new RuntimeException("Photo manquante : public/{$route['image']}. Vérifie le nom du fichier dans public/images/voyages/.");
            }
        }

        // 30 jours distincts parmi les 50 jours allant du 1 nov. au 20 déc.
        // La graine fixe rend les dates/heures identiques lors d'une nouvelle exécution.
        mt_srand(28102026);
        $offsets = range(0, 49);
        shuffle($offsets);
        $offsets = array_slice($offsets, 0, 30);
        sort($offsets, SORT_NUMERIC);

        $firstDay = Carbon::create(2026, 11, 1, 0, 0, 0);
        $minuteOptions = [0, 15, 30, 45];
        $vehicleAvailableAt = [];
        $driverAvailableAt = [];

        DB::transaction(function () use ($agency, $vehicles, $drivers, $routes, $offsets, $firstDay, $minuteOptions, &$vehicleAvailableAt, &$driverAvailableAt): void {
            foreach ($offsets as $index => $offset) {
                $route = $routes[$index % count($routes)];
                $hour = mt_rand(4, 22);
                // 22:00 est l'heure limite; ne génère pas 22:15 ou plus tard.
                $minute = $hour === 22 ? 0 : $minuteOptions[mt_rand(0, count($minuteOptions) - 1)];
                $departureAt = $firstDay->copy()
                    ->addDays((int) $offset)
                    ->setTime($hour, $minute, 0);
                $arrivalAt = $departureAt->copy()->addMinutes($route['duration']);

                // Ne pas planifier deux voyages simultanés avec le même véhicule.
                $vehicle = $vehicles->first(function (Vehicle $candidate) use ($vehicleAvailableAt, $departureAt): bool {
                    return !isset($vehicleAvailableAt[$candidate->id])
                        || $vehicleAvailableAt[$candidate->id]->lessThanOrEqualTo($departureAt);
                });
                if (!$vehicle) {
                    throw new RuntimeException('Aucun véhicule disponible pour ce calendrier de démonstration.');
                }
                $vehicleAvailableAt[$vehicle->id] = $arrivalAt->copy();

                // Affecter un chauffeur seulement s'il est libre; sinon laisser la
                // mission non affectée pour éviter de créer un conflit artificiel.
                $driver = $drivers->first(function (Driver $candidate) use ($driverAvailableAt, $departureAt): bool {
                    return !isset($driverAvailableAt[$candidate->id])
                        || $driverAvailableAt[$candidate->id]->lessThanOrEqualTo($departureAt);
                });
                if ($driver) {
                    $driverAvailableAt[$driver->id] = $arrivalAt->copy();
                }

                Trip::updateOrCreate(
                    [
                        'departure_city' => $route['from'],
                        'arrival_city' => $route['to'],
                        'departure_at' => $departureAt->format('Y-m-d H:i:s'),
                    ],
                    [
                        'agency_id' => $agency->id,
                        'vehicle_id' => $vehicle->id,
                        'driver_id' => $driver?->id,
                        'departure_address' => 'Gare routière, ' . $route['from'],
                        'arrival_address' => 'Gare routière, ' . $route['to'],
                        'arrival_at' => $arrivalAt->format('Y-m-d H:i:s'),
                        'image_path' => $route['image'],
                        'price' => $route['price'],
                        'currency' => 'MGA',
                        'status' => 'scheduled',
                        'notes' => 'Voyage de démonstration MonSiège. Prix et durée indicatifs.',
                    ]
                );
            }
        });

        $this->command?->newLine();
        $this->command?->info('30 voyages de démonstration créés ou mis à jour.');
        $this->command?->info('Départs entre le 01/11/2026 et le 20/12/2026, entre 04h00 et 22h00.');
        $this->command?->info('Les voyages existants n’ont pas été supprimés.');
        $this->command?->warn('Les prix et durées sont indicatifs : vérifie-les avant une utilisation autre que la démonstration.');
    }
}