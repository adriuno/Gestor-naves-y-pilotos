<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StarshipTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_returns_a_list_of_starships()
    {
        $response = $this->get('/starships');

        $response->assertStatus(200); // la respuesta debe ser OK

        $response->assertJsonStructure([
            '*' => ['id', 'name', 'model', 'image2_url'] // campos mínimos esperados
        ]);
    }
}
