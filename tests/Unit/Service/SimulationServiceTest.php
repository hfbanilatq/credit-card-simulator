<?php

namespace Service;

use App\Models\CreditCard;
use App\Models\Product;
use App\Models\Simulation;
use App\Service\SimulationService;
use App\Utils\DiscountCalculator;
use Mockery;
use PHPUnit\Framework\TestCase;

class SimulationServiceTest extends TestCase

{
    protected $discountCalculator;
    protected $simulation;
    protected $product;
    protected $creditCard;

    public function setUp(): void
    {
        parent::setUp();

        $this->discountCalculator = Mockery::mock(DiscountCalculator::class);
        $this->discountCalculator->shouldReceive('calculatePriceWithDiscount')->andReturn(100);
        $this->product = Mockery::mock(Product::class);
        $this->creditCard = Mockery::mock(CreditCard::class);
        $this->product->shouldIgnoreMissing();
        $this->creditCard->shouldIgnoreMissing();
        $this->simulation = Mockery::mock(Simulation::class);
        $this->simulation->shouldReceive('products')->andReturn($this->product);
        $this->simulation->shouldReceive('setProducts');
        $this->simulation->shouldReceive('setCreditCard');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * A basic unit test example.
     */
    public function testCreateSimulation()
    {

        $this->simulation->shouldReceive('create')->andReturnSelf();

        $this->product->shouldReceive('sync')->andReturnUsing(function ($ids) {
            $this->product->associatedIds = $ids;
        });
        $this->simulation->shouldReceive('products')->andReturn($this->product);


        $service = new SimulationService($this->discountCalculator, $this->simulation);

        $numberOfInstallments = 12;
        $creditCardId = 1;
        $products = [1, 2, 3];

        $result = $service->create($numberOfInstallments, $creditCardId, $products);

        $this->simulation->shouldHaveReceived('create')->once()
            ->with([
                'number_of_installments' => $numberOfInstallments,
                'credit_card_id' => $creditCardId,
            ]);

        $this->assertSame($this->simulation, $result);
    }

    public function testUpdateSimulation()
    {
        $this->simulation->shouldReceive('find')->with(1)->andReturn($this->simulation);
        $this->simulation->shouldReceive('update')->andReturnSelf();



        $this->product->shouldReceive('sync')->andReturnUsing(function ($ids) {
            $this->product->associatedIds = $ids;
        });
        $this->simulation->shouldReceive('products')->andReturn($this->product);

        $service = new SimulationService($this->discountCalculator, $this->simulation);

        $id = 1;
        $numberOfInstallments = 12;
        $creditCardId = 1;
        $products = [1, 2, 3];

        $result = $service->update($id, $numberOfInstallments, $creditCardId, $products);

        $this->simulation->shouldHaveReceived('update')->once()
            ->with([
                'number_of_installments' => $numberOfInstallments,
                'credit_card_id' => $creditCardId,
            ]);

        $this->assertSame($this->simulation, $result);
    }

    public function testUpdateSimulationWithNoProducts()
    {

        $this->simulation->shouldReceive('find')->with(1)->andReturn($this->simulation);
        $this->simulation->shouldReceive('update')->andReturnSelf();


        $products = [];

        $this->product->shouldReceive('sync')->never();

        $this->simulation->shouldReceive('products')->andReturn($this->product);

        $service = new SimulationService($this->discountCalculator, $this->simulation);

        $id = 1;
        $numberOfInstallments = 12;
        $creditCardId = 1;

        $result = $service->update($id, $numberOfInstallments, $creditCardId, $products);

        $this->simulation->shouldHaveReceived('update')->once()
            ->with([
                'number_of_installments' => $numberOfInstallments,
                'credit_card_id' => $creditCardId,
            ]);

        $this->assertSame($this->simulation, $result);
    }

    public function testDeleteSimulation()
    {
        $this->simulation->shouldReceive('find')->with(1)->andReturn($this->simulation);
        $this->simulation->shouldReceive('delete')->once()->andReturn(true);

        $service = new SimulationService($this->discountCalculator, $this->simulation);

        $id = 1;

        $result = $service->delete($id);

        $this->assertTrue($result);
    }

    public function testDeleteSimulationNotFound()
    {
        $discountCalculator = Mockery::mock(DiscountCalculator::class);
        $discountCalculator->shouldReceive('calculatePriceWithDiscount')->andReturn(100);

        $this->simulation->shouldReceive('find')->with(1)->andReturnNull(); // Simula que no se encuentra la simulación

        $service = new SimulationService($this->discountCalculator, $this->simulation);

        $id = 1;

        $result = $service->delete($id);

        $this->assertFalse($result);
    }

    public function testGetById()
    {
        $this->simulation->shouldReceive('find')->with(1)->andReturn($this->simulation);

        $service = new SimulationService($this->discountCalculator, $this->simulation);
        $result = $service->getById(1);

        $this->assertSame($this->simulation, $result);
    }

    public function testGetAll()
    {
        $simulations = [
            Mockery::mock(Simulation::class),
            Mockery::mock(Simulation::class),
            Mockery::mock(Simulation::class),
        ];

        $this->simulation->shouldReceive('all')->andReturn($simulations);

        $service = new SimulationService($this->discountCalculator, $this->simulation);
        $result = $service->getAll();

        $this->assertEquals($simulations, $result);
    }

}
