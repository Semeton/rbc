<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600">Manage and view all system notifications</p>
            </div>
            <div class="flex space-x-3">
                <flux:button wire:click="markAllAsRead" variant="outline">
                    <flux:icon name="check" class="w-4 h-4 mr-2" />
                    Mark All as Read
                </flux:button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <flux:icon name="bell" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Notifications</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->statistics['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Unread</p>
                        <p class="text-2xl font-bold text-red-600">{{ $this->statistics['unread'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <flux:icon name="check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Read</p>
                        <p class="text-2xl font-bold text-green-600">{{ $this->statistics['read'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-lg">
                        <flux:icon name="clock" class="w-6 h-6 text-gray-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Expired</p>
                        <p class="text-2xl font-bold text-gray-600">{{ $this->statistics['expired'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search notifications..." 
                        class="w-full"
                    />
                </div>
                
                <div>
                    <flux:select wire:model.live="type" class="w-full">
                        <option value="">All Types</option>
                        @foreach($this->notificationTypes as $notificationType)
                            <option value="{{ $notificationType->value }}">{{ $notificationType->getDisplayName() }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:select wire:model.live="priority" class="w-full">
                        <option value="">All Priorities</option>
                        @foreach($this->notificationPriorities as $priority)
                            <option value="{{ $priority->value }}">{{ $priority->getDisplayName() }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:select wire:model.live="status" class="w-full">
                        <option value="">All Status</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </flux:select>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="flex items-center space-x-4">
                    <flux:select wire:model.live="perPage" class="w-20">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </flux:select>
                    <span class="text-sm text-gray-600">per page</span>
                </div>

                <flux:button wire:click="clearFilters" variant="outline" size="sm">
                    Clear Filters
                </flux:button>
            </div>
        </div>

        <!-- Notifications Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Notification
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type & Priority
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->notifications as $notification)
                            <tr class="hover:bg-gray-50 {{ $notification->isUnread() ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <!-- Priority Indicator -->
                                        <div class="flex-shrink-0">
                                            <div class="w-3 h-3 rounded-full {{ $notification->priority_background_color_class }}"></div>
                                        </div>

                                        <!-- Notification Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $notification->title }}
                                                </p>
                                                @if($notification->isUnread())
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full ml-2"></div>
                                                @endif
                                            </div>
                                            
                                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                                {{ $notification->message }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $notification->type_display }}
                                        </span>
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $notification->priority_background_color_class }} {{ $notification->priority_color_class }}">
                                                {{ $notification->priority_display }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notification->isRead() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $notification->isRead() ? 'Read' : 'Unread' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>
                                        <div>{{ $notification->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs">{{ $notification->created_at->format('g:i A') }}</div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($notification->isRead())
                                        <flux:button 
                                            wire:click="markAsUnread({{ $notification->id }})" 
                                            variant="outline" 
                                            size="sm"
                                        >
                                            Mark Unread
                                        </flux:button>
                                    @else
                                        <flux:button 
                                            wire:click="markAsRead({{ $notification->id }})" 
                                            variant="outline" 
                                            size="sm"
                                        >
                                            Mark Read
                                        </flux:button>
                                    @endif
                                    
                                    <flux:button 
                                        wire:click="deleteNotification({{ $notification->id }})" 
                                        wire:confirm="Are you sure you want to delete this notification?"
                                        variant="outline" 
                                        size="sm"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        Delete
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <flux:icon name="bell" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications found</h3>
                                    <p class="text-gray-500">
                                        @if($search || $type || $priority || $status)
                                            Try adjusting your filters to see more notifications.
                                        @else
                                            You're all caught up! No notifications to display.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $this->notifications->links() }}
            </div>
        </div>
    </div>
</div>
