<?php

declare(strict_types=1);

namespace App\Maintenance\Controllers;

use App\Maintenance\Requests\StoreMaintenanceRequest;
use App\Maintenance\Requests\UpdateMaintenanceRequest;
use App\Maintenance\Services\MaintenanceService;
use App\Models\TruckMaintenanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MaintenanceController
{
    public function __construct(
        private readonly MaintenanceService $maintenanceService
    ) {}

    /**
     * Display a listing of maintenance records
     */
    public function index(): View
    {
        return view('maintenance.index');
    }

    /**
     * Show the form for creating a new maintenance record
     */
    public function create(): View
    {
        return view('maintenance.create');
    }

    /**
     * Store a newly created maintenance record
     */
    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $maintenance = $this->maintenanceService->createMaintenance($request->validated());

        return redirect()
            ->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance record created successfully.');
    }

    /**
     * Display the specified maintenance record
     */
    public function show(TruckMaintenanceRecord $maintenance): View
    {
        return view('maintenance.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified maintenance record
     */
    public function edit(TruckMaintenanceRecord $maintenance): View
    {
        return view('maintenance.edit', compact('maintenance'));
    }

    /**
     * Update the specified maintenance record
     */
    public function update(UpdateMaintenanceRequest $request, TruckMaintenanceRecord $maintenance): RedirectResponse
    {
        $this->maintenanceService->updateMaintenance($maintenance, $request->validated());

        return redirect()
            ->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance record updated successfully.');
    }

    /**
     * Remove the specified maintenance record from storage
     */
    public function destroy(TruckMaintenanceRecord $maintenance): RedirectResponse
    {
        $this->maintenanceService->deleteMaintenance($maintenance);

        return redirect()
            ->route('maintenance.index')
            ->with('success', 'Maintenance record deleted successfully.');
    }
}
