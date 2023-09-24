<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\CreditCardController;
use App\Models\Product;
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

        Product::factory()->count(2)->create();
    }

    public function testListProducts()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testStoreProduct()
    {
        $productData = [
            'reference' => 'ABC123',
            'imageUrl' => 'https://example.com/image.jpg',
            'description' => 'Producto de prueba',
            'price' => 50.0,
            'warehouse' => 'A123',
            'discount' => 10.0,
            'discountWithCreditCard' => 5.0,
        ];
        $response = $this->json('POST', '/api/products', $productData);

        // Verifica que la solicitud se haya realizado correctamente (código de respuesta 201)
        $response->assertStatus(201);

        // Verifica que la respuesta JSON contenga los datos esperados del producto creado
        $response->assertJson([
            'reference' => $productData['reference'],
            'imageUrl' => $productData['imageUrl'],
            'description' => $productData['description'],
            'price' => $productData['price'],
            'warehouse' => $productData['warehouse'],
            'discount' => $productData['discount'],
            'discountWithCreditCard' => $productData['discountWithCreditCard'],
        ]);

        // Verifica que el producto se haya creado en la base de datos
        $this->assertDatabaseHas('products', [
            'reference' => $productData['reference'],
        ]);
    }
}
