<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\CreditCardService;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    private CreditCardService $creditCardService;

    public function __construct(CreditCardService $creditCardService)
    {
        $this->creditCardService = $creditCardService;
    }
    public function list()
    {
        $simulations = $this->creditCardService->getAll();
        return response()->json($simulations);
    }

    public function show($id)
    {
        $simulation = $this->creditCardService->getById($id);

        if (!$simulation) {
            return response()->json(['message' => 'Simulation not found'], 404);
        }

        return response()->json($simulation);
    }


    public function store(Request $request)
    {
        $data = $request->json()->all();
        $creditCard = $this->creditCardService->create(
            $data['type'],
            $data['feeValue'],
            $data['maxValue'],
            $data['monthlyInterest'],
            $data['effectiveAnnualInterest'],
            $data['imageUrl']
        );

        return response()->json($creditCard, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();
        $creditCard = $this->creditCardService->update(
            $id,
            $data['type'],
            $data['feeValue'],
            $data['maxValue'],
            $data['monthlyInterest'],
            $data['effectiveAnnualInterest'],
            $data['imageUrl']
        );

        if (!$creditCard) {
            return response()->json(['message' => 'Credit Card not found'], 404);
        }

        return response()->json($creditCard);
    }

}
