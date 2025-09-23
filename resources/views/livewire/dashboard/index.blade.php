<div>
    <!-- Header with Quick Actions -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Dashboard</flux:heading>
                <flux:subheading>Welcome back! Here's what's happening with your business</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <livewire:notification-bell />
                <flux:button variant="outline" wire:click="refreshData">
                    <flux:icon name="arrow-path" />
                    Refresh
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <flux:heading size="lg" class="mb-4">Quick Actions</flux:heading>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">
            <a href="{{ route('customers.create') }}" class="group">
                <div class="rounded-lg bg-white p-4 shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-blue-300">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 group-hover:bg-blue-200 transition-colors duration-200">
                            <flux:icon name="user-plus" class="h-6 w-6 text-blue-600" />
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900">Add Customer</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('transactions.create') }}" class="group">
                <div class="rounded-lg bg-white p-4 shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-green-300">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 group-hover:bg-green-200 transition-colors duration-200">
                            <flux:icon name="document-plus" class="h-6 w-6 text-green-600" />
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900">New Transaction</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('payments.create') }}" class="group">
                <div class="rounded-lg bg-white p-4 shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-purple-300">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 group-hover:bg-purple-200 transition-colors duration-200">
                            <flux:icon name="banknotes" class="h-6 w-6 text-purple-600" />
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900">Record Payment</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('atcs.create') }}" class="group">
                <div class="rounded-lg bg-white p-4 shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-orange-300">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 group-hover:bg-orange-200 transition-colors duration-200">
                            <flux:icon name="document-text" class="h-6 w-6 text-orange-600" />
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900">Add ATC</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('maintenance.create') }}" class="group">
                <div class="rounded-lg bg-white p-4 shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-red-300">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 group-hover:bg-red-200 transition-colors duration-200">
                            <flux:icon name="wrench-screwdriver" class="h-6 w-6 text-red-600" />
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900">Log Maintenance</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reports.index') }}" class="group">
                <div class="rounded-lg bg-white p-4 shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-indigo-300">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 group-hover:bg-indigo-200 transition-colors duration-200">
                            <flux:icon name="chart-bar" class="h-6 w-6 text-indigo-600" />
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900">View Reports</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="mb-8">
        <flux:heading size="lg" class="mb-4">Key Metrics</flux:heading>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Customers -->
            <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="users" class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Customers</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->overviewStats['customers']['total'] }}</p>
                        <p class="text-sm text-gray-500">{{ $this->overviewStats['customers']['active'] }} active</p>
                    </div>
                </div>
            </div>

            <!-- Total Drivers -->
            <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="user-group" class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Drivers</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->overviewStats['drivers']['total'] }}</p>
                        <p class="text-sm text-gray-500">{{ $this->overviewStats['drivers']['active'] }} active</p>
                    </div>
                </div>
            </div>

            <!-- Total Trucks -->
            <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="truck" class="h-8 w-8 text-orange-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Trucks</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->overviewStats['trucks']['total'] }}</p>
                        <p class="text-sm text-gray-500">{{ $this->overviewStats['trucks']['active'] }} active</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="currency-dollar" class="h-8 w-8 text-purple-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($this->overviewStats['revenue']['this_month'], 2) }}</p>
                        @if($this->overviewStats['revenue']['growth_percentage'] > 0)
                            <p class="text-sm text-green-600">+{{ $this->overviewStats['revenue']['growth_percentage'] }}% vs last month</p>
                        @elseif($this->overviewStats['revenue']['growth_percentage'] < 0)
                            <p class="text-sm text-red-600">{{ $this->overviewStats['revenue']['growth_percentage'] }}% vs last month</p>
                        @else
                            <p class="text-sm text-gray-500">No change vs last month</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Summary -->
    <div class="mb-8">
        <flux:heading size="lg" class="mb-4">Today's Summary</flux:heading>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Today's Transactions -->
            <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="document-text" class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Today's Transactions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->quickStats['today']['transactions'] }}</p>
                        <p class="text-sm text-gray-500">₦{{ number_format($this->quickStats['today']['revenue'], 2) }} revenue</p>
                    </div>
                </div>
            </div>

            <!-- Today's Payments -->
            <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="banknotes" class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Today's Payments</p>
                        <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($this->quickStats['today']['payments'], 2) }}</p>
                        <p class="text-sm text-gray-500">Received today</p>
                    </div>
                </div>
            </div>

            <!-- Pending Items -->
            <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="exclamation-triangle" class="h-8 w-8 text-orange-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Items</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->pendingItems['pending_atcs'] + $this->pendingItems['outstanding_customers'] + $this->pendingItems['trucks_needing_maintenance'] }}</p>
                        <p class="text-sm text-gray-500">Requires attention</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Alerts -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Recent Activity -->
        <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Recent Activity</flux:heading>
                <a href="{{ route('transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            
            <div class="space-y-4">
                @forelse($this->recentActivity as $activity)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $activity['color'] }}-100">
                                <flux:icon name="{{ $activity['icon'] }}" class="h-4 w-4 text-{{ $activity['color'] }}-600" />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-400">{{ $activity['date']->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">₦{{ number_format($activity['amount'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <flux:icon name="document-text" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                        <p class="text-sm text-gray-500">No recent activity</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Alerts & Notifications -->
        <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Alerts & Notifications</flux:heading>
                <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            
            <div class="space-y-4">
                @if($this->pendingItems['pending_atcs'] > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="document-text" class="h-5 w-5 text-yellow-600" />
                            <div>
                                <p class="font-medium text-yellow-800">Pending ATCs</p>
                                <p class="text-sm text-yellow-600">Requires attention</p>
                            </div>
                        </div>
                        <span class="text-lg font-semibold text-yellow-800">{{ $this->pendingItems['pending_atcs'] }}</span>
                    </div>
                @endif

                @if($this->pendingItems['outstanding_customers'] > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 border border-red-200">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="users" class="h-5 w-5 text-red-600" />
                            <div>
                                <p class="font-medium text-red-800">Outstanding Balances</p>
                                <p class="text-sm text-red-600">Customers with pending payments</p>
                            </div>
                        </div>
                        <span class="text-lg font-semibold text-red-800">{{ $this->pendingItems['outstanding_customers'] }}</span>
                    </div>
                @endif

                @if($this->pendingItems['trucks_needing_maintenance'] > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-orange-50 border border-orange-200">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="wrench-screwdriver" class="h-5 w-5 text-orange-600" />
                            <div>
                                <p class="font-medium text-orange-800">Maintenance Due</p>
                                <p class="text-sm text-orange-600">Trucks needing maintenance</p>
                            </div>
                        </div>
                        <span class="text-lg font-semibold text-orange-800">{{ $this->pendingItems['trucks_needing_maintenance'] }}</span>
                    </div>
                @endif

                @if($this->pendingItems['pending_atcs'] == 0 && $this->pendingItems['outstanding_customers'] == 0 && $this->pendingItems['trucks_needing_maintenance'] == 0)
                    <div class="text-center py-8">
                        <flux:icon name="check-circle" class="mx-auto h-12 w-12 text-green-400 mb-4" />
                        <p class="text-sm text-gray-500">All caught up! No pending items.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
