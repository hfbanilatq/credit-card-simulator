<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'reference',
        'image_url',
        'description',
        'price',
        'warehouse',
        'discount',
        'discount_with_credit_card',
    ];
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

    public function setId($id): void
    {
        $this->attributes['id'] = $id;
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function setReference($reference): void
    {
        $this->attributes['reference'] = $reference;
    }

    public function getReference()
    {
        return $this->attributes['reference'];
    }

    public function setImageUrl($imageUrl): void
    {
        $this->attributes['image_url'] = $imageUrl;
    }

    public function getImageUrl()
    {
        return $this->attributes['image_url'];
    }

    public function setDescription($description): void
    {
        $this->attributes['description'] = $description;
    }

    public function getDescription()
    {
        return $this->attributes['description'];
    }

    public function setPrice($price): void
    {
        $this->attributes['price'] = $price;
    }

    public function getPrice()
    {
        return $this->attributes['price'];
    }

    public function setWarehouse($warehouse): void
    {
        $this->attributes['warehouse'] = $warehouse;
    }

    public function getWarehouse()
    {
        return $this->attributes['warehouse'];
    }

    public function setDiscount($discount): void
    {
        $this->attributes['discount'] = $discount;
    }

    public function getDiscount()
    {
        return $this->attributes['discount'];
    }

    public function setDiscountWithCreditCard($discountWithCreditCard): void
    {
        $this->attributes['discount_with_credit_card'] = $discountWithCreditCard;
    }

    public function getDiscountWithCreditCard()
    {
        return $this->attributes['discount_with_credit_card'];
    }
}
