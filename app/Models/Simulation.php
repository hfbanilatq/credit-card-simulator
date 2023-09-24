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

    public function setProducts($productIds): void
    {
        $this->products()->sync($productIds);
    }

    public function setCreditCard($id): void
    {
        $this->creditCard()->sync($id);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
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
        return $this->products;
    }

}
