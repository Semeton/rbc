<?php

declare(strict_types=1);

namespace App\TruckMovement\Controllers;

use App\Models\DailyTruckRecord;
use App\TruckMovement\Requests\StoreTruckMovementRequest;
use App\TruckMovement\Requests\UpdateTruckMovementRequest;
use App\TruckMovement\Services\TruckMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TruckMovementController
{
    public function __construct(
        private readonly TruckMovementService $truckMovementService
    ) {}

    /**
     * Display a listing of truck movements
     */
    public function index(): View
    {
        return view('truck-movements.index');
    }

    /**
     * Show the form for creating a new truck movement
     */
    public function create(): View
    {
        return view('truck-movements.create');
    }

    /**
     * Store a newly created truck movement
     */
    public function store(StoreTruckMovementRequest $request): RedirectResponse
    {
        $truckMovement = $this->truckMovementService->createTruckMovement($request->validated());

        return redirect()
            ->route('truck-movements.show', $truckMovement)
            ->with('success', 'Truck movement created successfully.');
    }

    /**
     * Display the specified truck movement
     */
    public function show(DailyTruckRecord $truckMovement): View
    {
        return view('truck-movements.show', compact('truckMovement'));
    }

    /**
     * Show the form for editing the specified truck movement
     */
    public function edit(DailyTruckRecord $truckMovement): View
    {
        return view('truck-movements.edit', compact('truckMovement'));
    }

    /**
     * Update the specified truck movement
     */
    public function update(UpdateTruckMovementRequest $request, DailyTruckRecord $truckMovement): RedirectResponse
    {
        $this->truckMovementService->updateTruckMovement($truckMovement, $request->validated());

        return redirect()
            ->route('truck-movements.show', $truckMovement)
            ->with('success', 'Truck movement updated successfully.');
    }

    /**
     * Remove the specified truck movement from storage
     */
    public function destroy(DailyTruckRecord $truckMovement): RedirectResponse
    {
        $this->truckMovementService->deleteTruckMovement($truckMovement);

        return redirect()
            ->route('truck-movements.index')
            ->with('success', 'Truck movement deleted successfully.');
    }
}
