<?php

declare(strict_types=1);

namespace App\ATC\Controllers;

use App\ATC\Requests\StoreATCRequest;
use App\ATC\Requests\UpdateATCRequest;
use App\ATC\Services\ATCService;
use App\Models\Atc;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ATCController
{
    public function __construct(
        private readonly ATCService $atcService
    ) {}

    /**
     * Display a listing of ATCs.
     */
    public function index(): View
    {
        return view('atcs.index');
    }

    /**
     * Show the form for creating a new ATC.
     */
    public function create(): View
    {
        return view('atcs.create');
    }

    /**
     * Store a newly created ATC in storage.
     */
    public function store(StoreATCRequest $request): RedirectResponse
    {
        $atc = $this->atcService->createATC($request->validated());

        return redirect()
            ->route('atcs.show', $atc)
            ->with('success', 'ATC created successfully.');
    }

    /**
     * Display the specified ATC.
     */
    public function show(Atc $atc): View
    {
        return view('atcs.show', compact('atc'));
    }

    /**
     * Show the form for editing the specified ATC.
     */
    public function edit(Atc $atc): View
    {
        return view('atcs.edit', compact('atc'));
    }

    /**
     * Update the specified ATC in storage.
     */
    public function update(UpdateATCRequest $request, Atc $atc): RedirectResponse
    {
        $this->atcService->updateATC($atc, $request->validated());

        return redirect()
            ->route('atcs.show', $atc)
            ->with('success', 'ATC updated successfully.');
    }

    /**
     * Remove the specified ATC from storage.
     */
    public function destroy(Atc $atc): RedirectResponse
    {
        $this->atcService->deleteATC($atc);

        return redirect()
            ->route('atcs.index')
            ->with('success', 'ATC deleted successfully.');
    }
}
