<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function store(Request $request)
    {
        $data = $request->json()->all();
        $product = $this->productService->create(
            $data['reference'],
            $data['image_url'],
            $data['description'],
            $data['price'],
            $data['warehouse'],
            $data['discount'],
            $data['discount_with_credit_card']
        );

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();
        $product = $this->productService->update(
            $id,
            $data['reference'],
            $data['image_url'],
            $data['description'],
            $data['price'],
            $data['warehouse'],
            $data['discount'],
            $data['discount_with_credit_card']
        );

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }


}
