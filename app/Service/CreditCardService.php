<?php

namespace App\Service;

use App\Models\CreditCard;

class CreditCardService
{
    public function create($type, $feeValue, $maxFee, $monthlyInterest, $effectiveAnnualInterest, $imageUrl)
    {
        return CreditCard::create([
            'type' => $type,
            'fee_value' => $feeValue,
            'max_fee' => $maxFee,
            'monthly_interest' => $monthlyInterest,
            'effective_annual_interest' => $effectiveAnnualInterest,
            'image_url' => $imageUrl,
        ]);
    }

    public function update($id, $type, $feeValue, $maxFee, $monthlyInterest, $effectiveAnnualInterest, $imageUrl)
    {
        $creditCard = CreditCard::find($id);
        if ($creditCard) {
            $creditCard->update([
                'type' => $type,
                'fee_value' => $feeValue,
                'max_fee' => $maxFee,
                'monthly_interest' => $monthlyInterest,
                'effective_annual_interest' => $effectiveAnnualInterest,
                'image_url' => $imageUrl,
            ]);
            return $creditCard;
        }
        return null;
    }

    public function delete($id)
    {
        $creditCard = CreditCard::find($id);
        if ($creditCard) {
            $creditCard->delete();
            return true;
        }
        return false;
    }

    public function getById($id)
    {
        return CreditCard::find($id);
    }

    public function getAll()
    {
        return CreditCard::all();
    }

}
