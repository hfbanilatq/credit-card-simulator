<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'reference' => $this->faker->unique()->word,
            'image_url' => $this->faker->imageUrl,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'warehouse' => $this->faker->randomNumber(3),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'discount_with_credit_card' => $this->faker->randomFloat(2, 0, 30),
        ];
    }
}

