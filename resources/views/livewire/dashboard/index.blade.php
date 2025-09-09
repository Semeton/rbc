<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Dashboard</flux:heading>
                <flux:subheading>Overview of your trucking business</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:field>
                    <flux:label>Chart Period</flux:label>
                    <flux:select wire:model.live="chartPeriod">
                        <flux:select.option value="7">Last 7 days</flux:select.option>
                        <flux:select.option value="30">Last 30 days</flux:select.option>
                        <flux:select.option value="90">Last 90 days</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:button variant="outline" wire:click="refreshData">
                    <flux:icon name="arrow-path" />
                    Refresh
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Customers -->
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="users" class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Customers</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->overviewStats['customers']['total'] }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $this->overviewStats['customers']['active'] }} active</p>
                    </div>
                </div>
            </div>

            <!-- Total Drivers -->
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="user-group" class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Drivers</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->overviewStats['drivers']['total'] }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $this->overviewStats['drivers']['active'] }} active</p>
                    </div>
                </div>
            </div>

            <!-- Total Trucks -->
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="truck" class="h-8 w-8 text-orange-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Trucks</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->overviewStats['trucks']['total'] }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $this->overviewStats['trucks']['active'] }} active</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="currency-dollar" class="h-8 w-8 text-purple-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Monthly Revenue</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->overviewStats['revenue']['this_month'], 2) }}</p>
                        @if($this->overviewStats['revenue']['growth_percentage'] > 0)
                            <p class="text-sm text-green-600">+{{ $this->overviewStats['revenue']['growth_percentage'] }}% vs last month</p>
                        @elseif($this->overviewStats['revenue']['growth_percentage'] < 0)
                            <p class="text-sm text-red-600">{{ $this->overviewStats['revenue']['growth_percentage'] }}% vs last month</p>
                        @else
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">No change vs last month</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mb-8">
        <flux:heading size="lg" class="mb-4">Quick Stats</flux:heading>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Today -->
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Today</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Transactions:</span>
                        <span class="font-medium">{{ $this->quickStats['today']['transactions'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Revenue:</span>
                        <span class="font-medium">₦{{ number_format($this->quickStats['today']['revenue'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Payments:</span>
                        <span class="font-medium">₦{{ number_format($this->quickStats['today']['payments'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- This Week -->
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">This Week</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Transactions:</span>
                        <span class="font-medium">{{ $this->quickStats['this_week']['transactions'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Revenue:</span>
                        <span class="font-medium">₦{{ number_format($this->quickStats['this_week']['revenue'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Payments:</span>
                        <span class="font-medium">₦{{ number_format($this->quickStats['this_week']['payments'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">This Month</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Transactions:</span>
                        <span class="font-medium">{{ $this->quickStats['this_month']['transactions'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Revenue:</span>
                        <span class="font-medium">₦{{ number_format($this->quickStats['this_month']['revenue'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Payments:</span>
                        <span class="font-medium">₦{{ number_format($this->quickStats['this_month']['payments'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Revenue Chart -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Revenue Trend</flux:heading>
                <span class="text-sm text-zinc-500 dark:text-zinc-400">Last {{ $chartPeriod }} days</span>
            </div>
            
            <div class="h-64 flex items-center justify-center">
                <div class="text-center text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="chart-bar" class="mx-auto h-12 w-12 mb-4" />
                    <p>Revenue chart will be displayed here</p>
                    <p class="text-sm">Data points: {{ count($this->revenueChart) }}</p>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Top Performers</flux:heading>
            
            <div class="space-y-6">
                <!-- Top Customers -->
                <div>
                    <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-3">Top Customers</h4>
                    <div class="space-y-2">
                        @forelse($this->topPerformers['customers'] as $customer)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-zinc-50 dark:bg-zinc-700">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $customer['name'] }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $customer['transactions'] }} transactions</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($customer['revenue'], 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">No data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Drivers -->
                <div>
                    <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-3">Top Drivers</h4>
                    <div class="space-y-2">
                        @forelse($this->topPerformers['drivers'] as $driver)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-zinc-50 dark:bg-zinc-700">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $driver['name'] }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $driver['transactions'] }} transactions</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($driver['revenue'], 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">No data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Pending Items -->
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Recent Activity -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Recent Activity</flux:heading>
            
            <div class="space-y-4">
                @forelse($this->recentActivity as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $activity['color'] }}-100 dark:bg-{{ $activity['color'] }}-900/20">
                                <flux:icon name="{{ $activity['icon'] }}" class="h-4 w-4 text-{{ $activity['color'] }}-600" />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $activity['title'] }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $activity['description'] }}</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $activity['date']->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($activity['amount'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No recent activity</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Items -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Pending Items</flux:heading>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                    <div class="flex items-center space-x-3">
                        <flux:icon name="document-text" class="h-5 w-5 text-yellow-600" />
                        <div>
                            <p class="font-medium text-yellow-800 dark:text-yellow-200">Pending ATCs</p>
                            <p class="text-sm text-yellow-600 dark:text-yellow-300">Requires attention</p>
                        </div>
                    </div>
                    <span class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">{{ $this->pendingItems['pending_atcs'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/20">
                    <div class="flex items-center space-x-3">
                        <flux:icon name="users" class="h-5 w-5 text-red-600" />
                        <div>
                            <p class="font-medium text-red-800 dark:text-red-200">Outstanding Balances</p>
                            <p class="text-sm text-red-600 dark:text-red-300">Customers with pending payments</p>
                        </div>
                    </div>
                    <span class="text-lg font-semibold text-red-800 dark:text-red-200">{{ $this->pendingItems['outstanding_customers'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <div class="flex items-center space-x-3">
                        <flux:icon name="wrench-screwdriver" class="h-5 w-5 text-orange-600" />
                        <div>
                            <p class="font-medium text-orange-800 dark:text-orange-200">Maintenance Due</p>
                            <p class="text-sm text-orange-600 dark:text-orange-300">Trucks needing maintenance</p>
                        </div>
                    </div>
                    <span class="text-lg font-semibold text-orange-800 dark:text-orange-200">{{ $this->pendingItems['trucks_needing_maintenance'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
