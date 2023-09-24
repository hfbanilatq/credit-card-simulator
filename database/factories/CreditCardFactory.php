<?php

namespace Database\Factories;

use App\Models\CreditCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditCardFactory extends Factory
{
    protected $model = CreditCard::class;

    public function definition()
    {
        return [
            'type' => $this->faker->word,
            'fee_value' => $this->faker->randomFloat(2, 0, 100),
            'max_fee' => $this->faker->numberBetween(1, 12),
            'monthly_interest' => $this->faker->randomFloat(2, 0, 20),
            'effective_annual_interest' => $this->faker->randomFloat(2, 0, 20),
            'image_url' => $this->faker->imageUrl(),
        ];
    }
}
