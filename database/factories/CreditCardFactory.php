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
            'name' => $this->faker->creditCardType,
            'monthly_interest' => $this->faker->randomFloat(2, 1, 5),
            'fee_value' => $this->faker->randomFloat(2, 0, 10),
        ];
    }
}

CreditCard::factory()->count(4)->create();
