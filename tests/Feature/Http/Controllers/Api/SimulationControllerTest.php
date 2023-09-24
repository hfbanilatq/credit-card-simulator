<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CreditCard;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SimulationControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function testSimulate()
    {
        // Crea dos productos y una tarjeta de crédito en tu base de datos
        $creditCard = CreditCard::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $simulationData = [
            'creditCardId' => $creditCard->id,
            'numberOfInstallments' => 12,
            'products' => [
                $product1->id,
                $product2->id,
            ],
        ];

        $response = $this->json('POST', '/api/simulations/simulate', $simulationData);

        $response->assertStatus(200);
        $this->assertCount(12, $response->json('monthlyFees'));
    }
}
