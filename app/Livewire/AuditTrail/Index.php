<?php

namespace App\Livewire\AuditTrail;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $module = '';

    public string $action = '';

    public string $user = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'module' => ['except' => ''],
        'action' => ['except' => ''],
        'user' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function mount(): void
    {
        // Set default date range to last 30 days
        if (empty($this->dateFrom) && empty($this->dateTo)) {
            $this->dateFrom = now()->subDays(30)->format('Y-m-d');
            $this->dateTo = now()->format('Y-m-d');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedModule(): void
    {
        $this->resetPage();
    }

    public function updatedAction(): void
    {
        $this->resetPage();
    }

    public function updatedUser(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->module = '';
        $this->action = '';
        $this->user = '';
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    #[Computed]
    public function auditTrails()
    {
        $query = AuditTrail::with('user')
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%'.$this->search.'%')
                    ->orWhere('action', 'like', '%'.$this->search.'%')
                    ->orWhere('module', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Apply module filter
        if (! empty($this->module)) {
            $query->where('module', $this->module);
        }

        // Apply action filter
        if (! empty($this->action)) {
            $query->where('action', $this->action);
        }

        // Apply user filter
        if (! empty($this->user)) {
            $query->where('user_id', $this->user);
        }

        // Apply date range filter
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->paginate(25);
    }

    #[Computed]
    public function modules()
    {
        return AuditTrail::distinct()
            ->pluck('module')
            ->filter()
            ->sort()
            ->values();
    }

    #[Computed]
    public function actions()
    {
        return AuditTrail::distinct()
            ->pluck('action')
            ->filter()
            ->sort()
            ->values();
    }

    #[Computed]
    public function users()
    {
        return User::whereHas('auditTrails')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function render(): View
    {
        return view('livewire.audit-trail.index');
    }
}
