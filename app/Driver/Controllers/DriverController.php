<?php

declare(strict_types=1);

namespace App\Driver\Controllers;

use App\Driver\Requests\StoreDriverRequest;
use App\Driver\Requests\UpdateDriverRequest;
use App\Driver\Services\DriverService;
use App\Models\Driver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DriverController
{
    public function __construct(
        private readonly DriverService $driverService
    ) {}

    /**
     * Display a listing of drivers.
     */
    public function index(): View
    {
        return view('drivers.index');
    }

    /**
     * Show the form for creating a new driver.
     */
    public function create(): View
    {
        return view('drivers.create');
    }

    /**
     * Store a newly created driver.
     */
    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $driver = $this->driverService->createDriver($request->validated());

        return redirect()
            ->route('drivers.show', $driver)
            ->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified driver.
     */
    public function show(Driver $driver): View
    {
        return view('drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the specified driver.
     */
    public function edit(Driver $driver): View
    {
        return view('drivers.edit', compact('driver'));
    }

    /**
     * Update the specified driver.
     */
    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $this->driverService->updateDriver($driver, $request->validated());

        return redirect()
            ->route('drivers.show', $driver)
            ->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified driver from storage.
     */
    public function destroy(Driver $driver): RedirectResponse
    {
        $this->driverService->deleteDriver($driver);

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }
}
