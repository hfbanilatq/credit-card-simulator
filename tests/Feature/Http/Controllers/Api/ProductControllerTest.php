<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\CreditCardController;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        factory(CreditCardController::class, 5)->create();
    }

    public function testListProducts()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }
}
