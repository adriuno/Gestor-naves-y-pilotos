<?php

namespace Database\Seeders;

use App\Models\Starship;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios
        $user1 = User::factory()->create([
            'username' => 'user22',
            'email' => 'prueba@asd.com',
            'admin' => true,
        ]);

        $user2 = User::factory()->create([
            'username' => 'normal',
            'email' => 'user@test.com',
            'admin' => false,
        ]);

        // 1. Importar naves desde la API (las base sin user_id)
        Artisan::call('importStarships');

        // 2. Obtener las naves base sin user_id
        $navesBase = Starship::whereNull('user_id')->with('pilots')->get();

        // 3. Clonar cada nave base para cada usuario y copiar pilotos
        foreach ([$user1, $user2] as $usuario) {
            foreach ($navesBase as $nave) {
                $clon = $usuario->starships()->create([
                    'name' => $nave->name,
                    'model' => $nave->model,
                    'manufacturer' => $nave->manufacturer,
                    'cost_in_credits' => $nave->cost_in_credits,
                    'image2_url' => $nave->image2_url,
                ]);

                // Copiar los pilotos vinculados a la nave original
                $clon->pilots()->attach($nave->pilots->pluck('id'));
            }
        }
    }
}
