<?php

declare(strict_types=1);

namespace App\Livewire\Notification;

use App\Models\Notification;
use App\Notification\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $priority = '';

    #[Url]
    public string $status = '';

    #[Url]
    public int $perPage = 15;

    private function getNotificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    public function getNotificationsProperty()
    {
        $request = new Request([
            'search' => $this->search,
            'type' => $this->type,
            'priority' => $this->priority,
            'status' => $this->status,
        ]);

        return $this->getNotificationService()->getPaginatedNotifications($request, $this->perPage);
    }

    public function getStatisticsProperty()
    {
        return $this->getNotificationService()->getNotificationStats();
    }

    public function getNotificationTypesProperty()
    {
        return \App\Enums\NotificationType::cases();
    }

    public function getNotificationPrioritiesProperty()
    {
        return \App\Enums\NotificationPriority::cases();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedPriority(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function markAsRead(Notification $notification): void
    {
        $this->getNotificationService()->markAsRead($notification);
        $this->dispatch('notification-read');
    }

    public function markAsUnread(Notification $notification): void
    {
        $this->getNotificationService()->markAsUnread($notification);
        $this->dispatch('notification-unread');
    }

    public function deleteNotification(Notification $notification): void
    {
        $this->getNotificationService()->deleteNotification($notification);
        $this->dispatch('notification-deleted');
    }

    public function markAllAsRead(): void
    {
        $this->getNotificationService()->markAllAsReadForUser(auth()->user());
        $this->dispatch('notifications-read');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->type = '';
        $this->priority = '';
        $this->status = '';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.notification.index');
    }
}
