<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Atc;
use App\Services\AtcAllocationValidator;
use Livewire\Component;
use Livewire\WithPagination;

class AtcAllocationManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all, available, fully_allocated, over_allocated
    public bool $showDetails = false;
    public ?Atc $selectedAtc = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        // Initialize component
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function showAtcDetails(Atc $atc): void
    {
        $this->selectedAtc = $atc;
        $this->showDetails = true;
    }

    public function closeDetails(): void
    {
        $this->showDetails = false;
        $this->selectedAtc = null;
    }

    public function getAtcsProperty()
    {
        $query = Atc::with(['transactions.customer', 'transactions.driver'])
            ->when($this->search, function ($query) {
                $query->where('atc_number', 'like', '%' . $this->search . '%')
                    ->orWhere('company', 'like', '%' . $this->search . '%');
            });

        $atcs = $query->paginate(10);

        // Add allocation data to each ATC
        $allocationValidator = app(AtcAllocationValidator::class);
        
        $atcs->getCollection()->transform(function (Atc $atc) use ($allocationValidator) {
            $atc->allocation_summary = $allocationValidator->getAllocationSummary($atc);
            return $atc;
        });

        // Apply filters
        if ($this->filter !== 'all') {
            $atcs->getCollection()->filter(function (Atc $atc) {
                return match ($this->filter) {
                    'available' => !$atc->allocation_summary['is_fully_allocated'] && !$atc->allocation_summary['is_over_allocated'],
                    'fully_allocated' => $atc->allocation_summary['is_fully_allocated'],
                    'over_allocated' => $atc->allocation_summary['is_over_allocated'],
                    default => true,
                };
            });
        }

        return $atcs;
    }

    public function getAllocationStatsProperty(): array
    {
        $allocationValidator = app(AtcAllocationValidator::class);
        $allAtcs = Atc::all();
        
        $stats = [
            'total_atcs' => $allAtcs->count(),
            'available' => 0,
            'fully_allocated' => 0,
            'over_allocated' => 0,
            'total_tons' => 0,
            'allocated_tons' => 0,
            'remaining_tons' => 0,
        ];

        foreach ($allAtcs as $atc) {
            $summary = $allocationValidator->getAllocationSummary($atc);
            
            $stats['total_tons'] += $summary['total_tons'];
            $stats['allocated_tons'] += $summary['allocated_tons'];
            $stats['remaining_tons'] += $summary['remaining_tons'];
            
            if ($summary['is_over_allocated']) {
                $stats['over_allocated']++;
            } elseif ($summary['is_fully_allocated']) {
                $stats['fully_allocated']++;
            } else {
                $stats['available']++;
            }
        }

        return $stats;
    }

    public function render()
    {
        return view('livewire.atc-allocation-manager', [
            'atcs' => $this->atcs,
            'allocationStats' => $this->allocationStats,
        ]);
    }
}