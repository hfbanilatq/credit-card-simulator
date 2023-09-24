<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'fee_value',
        'max_fee',
        'monthly_interest',
        'effective_annual_interest',
        'image_url',
    ];
    public function setId($id): void
    {
        $this->attributes['id'] = $id;
    }

    public function getId()
    {
        return $this->attributes['id'];
    }
    public function setName($name): void
    {
        $this->attributes['name'] = $name;
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function setMonthlyInterest($monthlyInterest): void
    {
        $this->attributes['monthly_interest'] = $monthlyInterest;
    }

    public function getMonthlyInterest()
    {
        return $this->attributes['monthly_interest'];
    }

    public function setFeeValue($feeValue): void
    {
        $this->attributes['fee_value'] = $feeValue;
    }

    public function getFeeValue()
    {
        return $this->attributes['fee_value'];
    }
}
