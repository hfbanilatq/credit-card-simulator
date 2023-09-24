<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;
    protected $visible = ['id',
    'fee_value',
    'max_fee',
    'monthly_interest',
    'effective_annualInterest',
    'image_url',
    'created_at',
    'updated_at'];

    protected $fillable = [
        'id',
        'type',
        'fee_value',
        'max_fee',
        'monthly_interest',
        'effective_annual_interest',
        'image_url',
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
    public function setType($value)
    {
        $this->attributes['type'] = $value;
    }

    public function getType()
    {
        return $this->attributes['type'];
    }

    public function setFeeValue($value)
    {
        $this->attributes['fee_value'] = $value;
    }

    public function getFeeValue()
    {
        return $this->attributes['fee_value'];
    }

    public function setMaxFee($value)
    {
        $this->attributes['maxFee'] = $value;
    }

    public function getMaxFee()
    {
        return $this->attributes['maxFee'];
    }

    public function setMonthlyInterest($value)
    {
        $this->attributes['monthlyInterest'] = $value;
    }

    public function getMonthlyInterest()
    {
        return $this->attributes['monthlyInterest'];
    }

    public function setEffectiveAnnualInterest($value)
    {
        $this->attributes['effective_annual_interest'] = $value;
    }

    public function getEffectiveAnnualInterest()
    {
        return $this->attributes['effective_annual_interest'];
    }

    public function setImageUrl($value)
    {
        $this->attributes['image_url'] = $value;
    }

    public function getImageUrl()
    {
        return $this->attributes['image_url'];
    }
}
