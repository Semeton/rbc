<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    <!-- Notification Bell Button -->
    <button 
        wire:click="toggleDropdown"
        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-full transition-colors duration-200"
        :class="{ 'text-blue-600': open }"
    >
        <flux:icon name="bell" class="w-6 h-6" />
        
        <!-- Unread Count Badge -->
        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                @if($this->unreadCount > 0)
                    <button 
                        wire:click="markAllAsRead"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($this->recentNotifications as $notification)
                <div 
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150 cursor-pointer"
                    wire:click="markAsRead({{ $notification->id }})"
                    :class="{ 'bg-blue-50': !{{ $notification->isRead() ? 'true' : 'false' }} }"
                >
                    <div class="flex items-start space-x-3">
                        <!-- Priority Indicator -->
                        <div class="flex-shrink-0">
                            <div class="w-2 h-2 rounded-full {{ $notification->priority_background_color_class }}"></div>
                        </div>

                        <!-- Notification Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $notification->title }}
                                </p>
                                <span class="text-xs text-gray-500 ml-2">
                                    {{ $notification->time_ago }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                {{ $notification->message }}
                            </p>
                            
                            <div class="flex items-center justify-between mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $notification->priority_background_color_class }} {{ $notification->priority_color_class }}">
                                    {{ $notification->type_display }}
                                </span>
                                
                                @if($notification->isUnread())
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <flux:icon name="bell" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">No notifications</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($this->recentNotifications->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200">
                <a 
                    href="{{ route('notifications.index') }}" 
                    class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>
