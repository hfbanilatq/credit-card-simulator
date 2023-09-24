<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\SimulationService;

class SimulationController extends Controller
{
    private SimulationService $simulationService;

    public function __construct(SimulationService $simulationService)
    {
        $this->simulationService = $simulationService;
    }

    public function list()
    {
        $simulations = $this->simulationService->getAll();
        return response()->json($simulations);
    }

    public function show($id)
    {
        $simulation = $this->simulationService->getById($id);

        if (!$simulation) {
            return response()->json(['message' => 'Simulation not found'], 404);
        }

        return response()->json($simulation);
    }

    public function store(Request $request)
    {
        $data = $request->json()->all();
        $simulation = $this->simulationService->create(
        $data['numberOfInstallments'],
        $data['creditCardId'],
        $data['products']
    );

        return response()->json($simulation, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();
        $simulation = $this->simulationService->update($id,
            $data['numberOfInstallments'],
            $data['creditCardId'],
            $data['products']
        );

        if (!$simulation) {
            return response()->json(['message' => 'Simulation not found'], 404);
        }

        return response()->json($simulation);
    }

    public function destroy($id)
    {
        $deleted = $this->simulationService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Simulation not found'], 404);
        }

        return response()->json(['message' => 'Simulation deleted']);
    }

    public function simulate(Request $request)
    {
        $data = $request->json()->all();
        $creditCardId = $data['creditCardId'];
        $products = $data['products'];
        $numberOfInstallments = $data['numberOfInstallments'];

        $simulationSaved = $this->simulationService->create(
             $numberOfInstallments,
             $creditCardId,
             $products,
        );
        $simulationResult =  $this->simulationService->simulate($simulationSaved);

        return response()->json($simulationResult);
    }
}

