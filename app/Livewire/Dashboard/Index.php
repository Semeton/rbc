<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Dashboard\DashboardService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        // Initialize dashboard
    }

    #[Computed]
    public function overviewStats()
    {
        return app(DashboardService::class)->getOverviewStats();
    }

    #[Computed]
    public function recentActivity()
    {
        return app(DashboardService::class)->getRecentActivity(10);
    }

    #[Computed]
    public function topPerformers()
    {
        return app(DashboardService::class)->getTopPerformers();
    }

    #[Computed]
    public function revenueChart()
    {
        return app(DashboardService::class)->getRevenueChart(30);
    }

    #[Computed]
    public function pendingItems()
    {
        return app(DashboardService::class)->getPendingItems();
    }

    #[Computed]
    public function quickStats()
    {
        return app(DashboardService::class)->getQuickStats();
    }

    public function refreshData(): void
    {
        // Force refresh of computed properties
        unset($this->overviewStats);
        unset($this->recentActivity);
        unset($this->topPerformers);
        unset($this->revenueChart);
        unset($this->pendingItems);
        unset($this->quickStats);
    }

    public function render(): View
    {
        return view('livewire.dashboard.index');
    }
}
