<?php

namespace Tests\Feature;

use App\Models\Pilot;
use App\Models\Starship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddPilotTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_add_pilot(): void
    {

        // ✅ Asegúrate de tener estos datos en la base (tras ejecutar el comando)
        $pilot = Pilot::first();
        $starship = Starship::first();

        $this->assertNotNull($pilot, 'No hay pilotos en la base de datos');
        $this->assertNotNull($starship, 'No hay naves en la base de datos');

        // 🔧 Asegurarse de que no estén ya vinculados
        $starship->pilots()->detach($pilot->id);


        // 1️⃣ Vincular el piloto a la nave
        $response = $this->post("/starships/{$starship->id}/pilots", [
            'pilot_id' => $pilot->id,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'reason' => 'pilot_attached',
        ]);

        // 2️⃣ No debería dejar volver a vincularlo
        $responseRepeat = $this->post("/starships/{$starship->id}/pilots", [
            'pilot_id' => $pilot->id,
        ]);

        $responseRepeat->assertStatus(409);
        $responseRepeat->assertJson([
            'status' => 'error',
            'reason' => 'already_attached',
        ]);
    }
}
