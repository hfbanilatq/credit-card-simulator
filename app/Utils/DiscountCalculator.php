<?php

namespace App\Utils;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
class DiscountCalculator
{
    public function calculatePriceWithDiscount($price, $discount)
    {
        if ($discount > 0) {
            $discountDecimal = $discount / 100;
            $discountValue = $price * $discountDecimal;
            return $price - $discountValue;
        } else {
            return $price;
        }
    }
}
