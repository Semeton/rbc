<?php

declare(strict_types=1);

namespace App\Truck\Controllers;

use App\Models\Truck;
use App\Truck\Requests\StoreTruckRequest;
use App\Truck\Requests\UpdateTruckRequest;
use App\Truck\Services\TruckService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TruckController
{
    public function __construct(
        private readonly TruckService $truckService
    ) {}

    /**
     * Display a listing of trucks.
     */
    public function index(): View
    {
        return view('trucks.index');
    }

    /**
     * Show the form for creating a new truck.
     */
    public function create(): View
    {
        return view('trucks.create');
    }

    /**
     * Store a newly created truck.
     */
    public function store(StoreTruckRequest $request): RedirectResponse
    {
        $truck = $this->truckService->createTruck($request->validated());

        return redirect()
            ->route('trucks.show', $truck)
            ->with('success', 'Truck created successfully.');
    }

    /**
     * Display the specified truck.
     */
    public function show(Truck $truck): View
    {
        return view('trucks.show', compact('truck'));
    }

    /**
     * Show the form for editing the specified truck.
     */
    public function edit(Truck $truck): View
    {
        return view('trucks.edit', compact('truck'));
    }

    /**
     * Update the specified truck.
     */
    public function update(UpdateTruckRequest $request, Truck $truck): RedirectResponse
    {
        $this->truckService->updateTruck($truck, $request->validated());

        return redirect()
            ->route('trucks.show', $truck)
            ->with('success', 'Truck updated successfully.');
    }

    /**
     * Remove the specified truck from storage.
     */
    public function destroy(Truck $truck): RedirectResponse
    {
        $this->truckService->deleteTruck($truck);

        return redirect()
            ->route('trucks.index')
            ->with('success', 'Truck deleted successfully.');
    }
}
