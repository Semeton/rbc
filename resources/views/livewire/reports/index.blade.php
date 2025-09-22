<div>
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Reports Dashboard</flux:heading>
                <flux:subheading>Access all business reports and analytics</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button variant="outline" icon="arrow-path">
                    Refresh
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Reports</p>
                    <p class="text-3xl font-bold">{{ count($this->reportTypes) }}</p>
                </div>
                <flux:icon name="chart-bar" class="h-8 w-8 text-blue-200" />
            </div>
        </div>
        
        <div class="rounded-lg bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Financial Reports</p>
                    <p class="text-3xl font-bold">6</p>
                </div>
                <flux:icon name="currency-dollar" class="h-8 w-8 text-green-200" />
            </div>
        </div>
        
        <div class="rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Performance Reports</p>
                    <p class="text-3xl font-bold">4</p>
                </div>
                <flux:icon name="presentation-chart-bar" class="h-8 w-8 text-purple-200" />
            </div>
        </div>
        
        <div class="rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Operational Reports</p>
                    <p class="text-3xl font-bold">2</p>
                </div>
                <flux:icon name="cog-6-tooth" class="h-8 w-8 text-orange-200" />
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach($this->reportTypes as $key => $report)
            @php
                $color = $report['color'];
                $icon = $report['icon'];
                $route = $report['route'];
                $name = $report['name'];
                $description = $report['description'];

                $cardRing = "hover:ring-2 hover:ring-{$color}-500";
                $cardBg = "bg-white dark:bg-zinc-800";
                $cardRingBase = "ring-1 ring-zinc-200 dark:ring-zinc-700";
                $iconBg = "bg-{$color}-100 dark:bg-{$color}-900/20";
                $iconColor = "text-{$color}-600 dark:text-{$color}-400";
                $badgeBg = "bg-{$color}-100 dark:bg-{$color}-900/20";
                $badgeText = "text-{$color}-800 dark:text-{$color}-400";
                $buttonText = "hover:text-{$color}-600 dark:hover:text-{$color}-400";
                $hoverEffect = "from-{$color}-500/5";
            @endphp
            <div class="group relative overflow-hidden rounded-xl {{ $cardBg }} shadow-sm {{ $cardRingBase }} transition-all duration-200 hover:shadow-lg {{ $cardRing }}">
                <!-- Card Header -->
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $iconBg }}">
                                <flux:icon name="presentation-chart-bar" class="h-5 w-5 {{ $iconColor }}" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $name }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
                </div>

                <!-- Card Footer -->
                <div class="border-t border-zinc-200 bg-zinc-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center rounded-full {{ $badgeBg }} px-2 py-1 text-xs font-medium {{ $badgeText }}">
                                {{ ucfirst($name) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <flux:button 
                                variant="ghost" 
                                size="sm" 
                                href="{{ route($route) }}"
                                class="text-zinc-600 dark:text-zinc-400 {{ $buttonText }}"
                            >
                                <flux:icon name="arrow-right" class="h-4 w-4" />
                            </flux:button>
                        </div>
                    </div>
                </div>

                <!-- Hover Effect -->
                <div class="absolute inset-0 bg-gradient-to-r {{ $hoverEffect }} to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100 pointer-events-none"></div>
            </div>
        @endforeach
    </div>

    <!-- Recent Activity Section -->
    <div class="mt-12">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Quick Actions</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Commonly used report actions</p>
        </div>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Quick Financial Overview -->
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/20">
                        <flux:icon name="currency-dollar" class="h-5 w-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Financial Overview</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Quick financial reports</p>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <flux:button variant="outline" size="sm" :href="route('reports.customer-balance')">
                        Balances
                    </flux:button>
                    <flux:button variant="outline" size="sm" :href="route('reports.cash-flow')">
                        Cash Flow
                    </flux:button>
                </div>
            </div>

            <!-- Performance Reports -->
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                        <flux:icon name="presentation-chart-bar" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Performance</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Driver & depot performance</p>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <flux:button variant="outline" size="sm" :href="route('reports.driver-performance')">
                        Drivers
                    </flux:button>
                    <flux:button variant="outline" size="sm" :href="route('reports.depot-performance')">
                        Depots
                    </flux:button>
                </div>
            </div>

            <!-- Operational Reports -->
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                        <flux:icon name="truck" class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Operations</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Truck & maintenance</p>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <flux:button variant="outline" size="sm" :href="route('reports.truck-utilization')">
                        Utilization
                    </flux:button>
                    <flux:button variant="outline" size="sm" :href="route('reports.truck-maintenance-cost')">
                        Maintenance
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-12 rounded-lg bg-zinc-50 p-6 dark:bg-zinc-800/50">
        <div class="flex items-start space-x-4">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                <flux:icon name="question-mark-circle" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Need Help?</h3>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    Each report provides detailed analytics and can be exported to PDF or Excel. 
                    Use the filters in individual reports to customize your data view.
                </p>
            </div>
        </div>
    </div>
</div>