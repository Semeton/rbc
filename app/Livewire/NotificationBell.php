<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Notification;
use App\Models\User;
use App\Notification\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $showDropdown = false;

    private function getNotificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    #[Computed]
    public function unreadCount(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        return $this->getNotificationService()->getUnreadCount(Auth::user());
    }

    #[Computed]
    public function recentNotifications()
    {
        if (!Auth::check()) {
            return collect();
        }

        return $this->getNotificationService()->getUserNotifications(
            Auth::user(),
            limit: 5,
            unreadOnly: false
        );
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = Notification::find($notificationId);
        
        if ($notification && Auth::check()) {
            // Check if user can access this notification
            if ($notification->user_id === Auth::id() || $notification->user_id === null) {
                $this->getNotificationService()->markAsRead($notification);
                $this->dispatch('notification-read');
            }
        }
    }

    public function markAllAsRead(): void
    {
        if (Auth::check()) {
            $this->getNotificationService()->markAllAsReadForUser(Auth::user());
            $this->dispatch('notifications-read');
        }
    }

    public function render(): View
    {
        return view('livewire.notification-bell');
    }
}
