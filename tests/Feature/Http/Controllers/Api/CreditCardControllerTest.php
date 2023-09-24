<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CreditCard;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditCardControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations; // Ejecuta las migraciones antes de las pruebas

    protected function setUp(): void
    {
        parent::setUp();

        CreditCard::factory()->count(2)->create();
    }

    public function testListCreditCards(): void
    {
        $response = $this->get('/api/credit-cards');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testShowCreditCard(): void
    {
        $creditCard = CreditCard::first(); // Obtén el primer producto de la base de datos

        $response = $this->get('/api/credit-cards/' . $creditCard->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $creditCard->id,
            'maxFee' => $creditCard->max_fee,
        ]);
    }

}
