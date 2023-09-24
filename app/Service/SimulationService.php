<?php

namespace App\Service;

use App\Models\CreditCard;
use App\Models\Simulation;
use App\Utils\DiscountCalculator;

use Illuminate\Support\Collection;

class SimulationService
{
    private DiscountCalculator $discountCalculator;
    private Simulation $simulation;

    public function __construct(DiscountCalculator $discountCalculator, Simulation $simulation = null)
    {
        $this->discountCalculator = $discountCalculator;
        $this->simulation = $simulation ?: new Simulation(); // Si no se proporciona una instancia de Simulation, se crea una nueva
    }
    public function create($numberOfInstallments, $creditCardId, $products)
    {
        $simulation = $this->simulation->create([
            'number_of_installments' => $numberOfInstallments,
            'credit_card_id' => $creditCardId,
        ]);

        $simulation->products()->attach($products);

        return $simulation;
    }


    public function update($id, $numberOfInstallments, $creditCardId, $products)
    {
        $simulation = $this->simulation->find($id);
        if ($simulation) {
            $data = [
                'number_of_installments' => $numberOfInstallments,
                'credit_card_id' => $creditCardId,
            ];
            $simulation->update($data);

            if (!empty($products)) {
                $simulation->products()->attach($products);
            } else {
                $simulation->products()->detach();
            }

            return $simulation;
        }
        return null;
    }


    public function delete($id): bool
    {
        $simulation = $this->simulation->find($id);
        if ($simulation) {
            $simulation->delete();
            return true;
        }
        return false;
    }

    public function getById($id)
    {
        return $this->simulation->find($id);
    }

    public function getAll()
    {
        return $this->simulation->all();
    }

    public function simulate(Simulation $simulation): array
    {
        $realPrice = 0;
        $promotionPrice = 0;
        $priceWithCreditCard = 0;

        $products = $simulation->getProducts();

        foreach ($products as $product) {
            $realPrice += $product->getPrice();
            $promotionPrice += $this->discountCalculator->calculatePriceWithDiscount(
                $product->getPrice(),
                $product->getDiscount()
            );
            $priceWithCreditCard += $this->discountCalculator->calculatePriceWithDiscount(
                $product->getPrice(),
                $product->getDiscountWithCreditCard()
            );
        }

        $creditCard = CreditCard::find($simulation->getCreditCardId());
        $fees = new Collection();
        $creditCardInterest = $creditCard->monthly_interest / 100;
        $numberOfInstallment = 1;
        $balance = $priceWithCreditCard;
        $feeMonthly = $priceWithCreditCard / $simulation->getNumberOfInstallments();
        $pricePaid = 0;

        for ($i = 0; $i < $simulation->getNumberOfInstallments(); $i++) {
            $feePrice = match ($numberOfInstallment) {
                1 => $feeMonthly,
                2 => $feeMonthly + ($priceWithCreditCard * $creditCardInterest) + ($balance * $creditCardInterest),
                default => $feeMonthly + ($balance * $creditCardInterest),
            };

            if ($creditCard->fee_value > 0) {
                $feePrice += $creditCard->fee_value;
            }

            $pricePaid += $feePrice;
            $balance -= $feeMonthly;

            $fees->push([
                'balance' => $balance,
                'feeNumber' => $numberOfInstallment,
                'feeValue' => $feePrice,
            ]);

            $numberOfInstallment++;
        }

        return [
            'promotionPrice' => $promotionPrice,
            'monthlyFees' => $fees,
            'pricePaid' => $pricePaid,
            'priceWithCreditCard' => $priceWithCreditCard,
            'realPrice' => $realPrice,
        ];
    }
}
