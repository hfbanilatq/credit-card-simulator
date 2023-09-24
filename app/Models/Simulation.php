<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    use HasFactory;
    protected $fillable = [
        'number_of_installments',
        'credit_card_id',
    ];
    public function creditCard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CreditCard::class, 'credit_card_id');
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $transformedData = [];

        foreach ($data as $key => $value) {
            $camelCaseKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
            $transformedData[$camelCaseKey] = $value;
        }

        return $transformedData;
    }

    public function setProducts($productIds): void
    {
        $this->products()->attach($productIds);
    }

    public function setCreditCard($id): void
    {
        $this->creditCard()->associate($id);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_has_simulation');
    }

    public function setNumberOfInstallments($numberOfInstallments): void
    {
        $this->attributes['number_of_installments'] = $numberOfInstallments;
    }

    public function getNumberOfInstallments()
    {
        return $this->attributes['number_of_installments'];
    }

    public function setCreditCardId($creditCardId): void
    {
        $this->attributes['credit_card_id'] = $creditCardId;
    }

    public function getCreditCardId()
    {
        return $this->attributes['credit_card_id'];
    }

    public function getCreditCard()
    {
        return $this->creditCard;
    }

    public function getProducts()
    {
        return $this->products()->get();
    }

}
