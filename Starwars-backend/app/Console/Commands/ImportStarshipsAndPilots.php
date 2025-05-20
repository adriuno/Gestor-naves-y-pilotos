<?php

namespace App\Console\Commands;

use App\Models\Pilot;
use App\Models\Starship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportStarshipsAndPilots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importStarships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import starships and pilots from SWAPI';

    /**
     * Execute the console command.
     */


    // -----------------  FUNCIONES  -------------------


    public function handle()
    {

        // Limpiar los datos al llamar comando:
        // Artisan::call('migrate:fresh'); ------ Esto borra TODAS las tablas y las vuelve a crear (no recomendado para producción)
        DB::table('pilot_starship')->delete();
        Pilot::query()->delete();
        Starship::query()->delete();



        // Cargar los personajes con sus imágenes desde la API de Akabab
        $characters = Http::withOptions(['verify' => false])
            ->get('https://akabab.github.io/starwars-api/api/all.json')
            ->json();

        // SWAPI: Empezamos a leer las starships
        $nextPage = 'https://swapi.dev/api/starships/';

        while ($nextPage) {
            $this->info(" Obteniendo datos de: $nextPage");

            $response = Http::withOptions(['verify' => false])->get($nextPage);
            //  Esto así proque daba error en teoría por certificados ssl y xampp (info sacada de ChatGpt)
            $data = $response->json();

            foreach ($data['results'] as $shipData) {
                $this->importStarship($shipData, $characters);
            }

            // Avanzar a la siguiente página (si existe)
            $nextPage = $data['next'];
        }
    }


    private function importStarship(array $shipData, array $characters)
    {
        //  Guardar la nave
        $starship = Starship::create([
            'name' => $shipData['name'] ?? null,
            'model' => $shipData['model'] ?? null,
            'manufacturer' => $shipData['manufacturer'] ?? null,
            'cost_in_credits' => is_numeric($shipData['cost_in_credits']) ? $shipData['cost_in_credits'] : null,
            // 'starship_class' => $shipData['starship_class'] ?? null,
            'image2_url' => $this->getStarshipImage($shipData['name']),
        ]);


        // Importar pilotos de esa nave
        foreach ($shipData['pilots'] as $pilotUrl) {
            $pilotData = Http::withOptions(['verify' => false])->get($pilotUrl)->json();

            $pilot = Pilot::firstOrCreate(
                ['name' => $pilotData['name']],
                [
                    'gender' => $pilotData['gender'] ?? null,
                    'films' => json_encode($pilotData['films'] ?? []),
                    'image_url' => $this->getPilotImage($pilotData['name'], $characters),
                ]
            );

            // Relacionar piloto con la nave ----- Así puedes importar todas las naves y pilotos sin preocuparte de duplicados ni perder relaciones anteriores.
            // se podría hacer attach o sync pero podría dar error de duplicados o bien sync que elimna los anteriores.....
            $starship->pilots()->syncWithoutDetaching([$pilot->id]);
        }
    }

    private function getPilotImage(string $name, array $characters): ?string
    {
        foreach ($characters as $char) {
            if (strcasecmp($char['name'], $name) === 0) {
                return $char['image'];
            }
        }
        // Imagen por defecto si no se encuentra en el array de personajes
        return asset('storage/images/pilotoAleatorio.jpg');
    }

    private function getStarshipImage(string $name): string
    {
        $images = [
            'CR90 corvette' => 'C90.webp',
            'Star Destroyer' => 'STAR.jpg',
            'Sentinel-class landing craft' => 'Sentinel.webp',
            'Death Star' => 'DeathStar2.webp',
            'Millennium Falcon' => 'halcon.webp',
            'Y-wing' => 'ywing.jpeg',
            'X-wing' => 'xwing.webp',
            'TIE Advanced x1' => 'tie.webp',
            'Executor' => 'executor.webp',
            'Rebel transport' => 'rebeltransport.webp',
            'Slave 1' => 'slave1.jpg',
            'Imperial shuttle' => 'imperialshutter.jpg',
            'EF76 Nebulon-B escort frigate' => 'nebulonb.webp',
            'Calamari Cruiser' => 'calamari.jpg',
            'A-wing' => 'awing.webp',
            'B-wing' => 'bwing.jpeg',
            'Republic Cruiser' => 'republic.jpeg',
            'Droid control ship' => 'droidship.webp',
            'Naboo fighter' => 'naboofig.webp',
            'Naboo Royal Starship' => 'nabooship.jpg',
            'Scimitar' => 'scimitar.webp',
            'J-type diplomatic barge' => 'asd.webp',
            'AA-9 Coruscant freighter' => 'aa9.webp',
            'Jedi starfighter' => 'jedistar.jpg',
            'H-type Nubian yacht' => 'yacht.webp',
            'Starfreighter' => 'starfreighter.jpg',
            'Trade Federation cruiser' => 'tradecruiser.webp',
            'Theta-class T-2c shuttle' => 'theta.jfif',
            'Republic attack cruiser' => 'republicattack.webp',
            'Naboo star skiff' => 'skiff.png',
            'Jedi Interceptor' => 'interceptor.webp',
            'arc-170' => 'arc.webp',
            'Banking clan frigte' => 'frigte.webp',
            'Belbullab-22 starfighter' => 'bellbu.webp',
            'V-wing' => 'vwing.webp',
            'Solar Sailer' => 'solars.webp',
            'Republic Assault ship' => 'repaship.webp',
        ];

        // Si existe imagen personalizada para esa nave
        if (isset($images[$name])) {
            return asset('storage/images/' . $images[$name]);
        }

        // si no... no imagen!!
        return $images[$name] ?? 'https://placehold.co/600x400/000000/FFFFFF.png';
    }
}
