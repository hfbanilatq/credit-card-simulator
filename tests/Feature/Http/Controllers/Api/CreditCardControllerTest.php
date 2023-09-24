<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\CreditCardController;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreditCardControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations; // Ejecuta las migraciones antes de las pruebas

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos de prueba utilizando factories
        factory(CreditCardController::class, 5)->create();
    }

    public function testListProducts()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

}
