<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CreditCard;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulationControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function testSimulate(): void
    {
        /** @var CreditCard $creditCard */
        $creditCard = CreditCard::factory()->create();
        /** @var Product $product1 */
        $product1 = Product::factory()->create();

        /** @var Product $product2 */
        $product2 = Product::factory()->create();

        $simulationData = [
            'creditCardId' => $creditCard->getId(),
            'numberOfInstallments' => 12,
            'products' => [
                $product1->getId(),
                $product2->getId(),
            ],
        ];

        $response = $this->json('POST', '/api/simulations/simulate', $simulationData);

        $response->assertStatus(200);
        $this->assertCount(12, $response->json('monthlyFees'));
    }
}
