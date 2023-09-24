<?php

namespace App\Service;

use App\Models\Product;

class ProductService
{
    public function create($reference, $imageUrl, $description, $price, $warehouse, $discount, $discountWithCreditCard)
    {
        return Product::create([
            'reference' => $reference,
            'image_url' => $imageUrl,
            'description' => $description,
            'price' => $price,
            'warehouse' => $warehouse,
            'discount' => $discount,
            'discount_with_credit_card' => $discountWithCreditCard,
        ]);
    }

    public function update($id, $reference, $imageUrl, $description, $price, $warehouse, $discount, $discountWithCreditCard)
    {
        $product = Product::find($id);
        if ($product) {
            $product->update([
                'reference' => $reference,
                'image_url' => $imageUrl,
                'description' => $description,
                'price' => $price,
                'warehouse' => $warehouse,
                'discount' => $discount,
                'discount_with_credit_card' => $discountWithCreditCard,
            ]);
            return $product;
        }
        return null;
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return true;
        }
        return false;
    }

    public function getById($id)
    {
        return Product::find($id);
    }

    public function getAll()
    {
        return Product::all();
    }
}
