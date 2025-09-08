<div>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')]
    ]" />

    <!-- Dashboard Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <x-dashboard-widget 
            title="Total Customers" 
            :value="$stats['total_customers']" 
            icon="users" 
            color="blue" 
        />
        <x-dashboard-widget 
            title="Active Drivers" 
            :value="$stats['active_drivers']" 
            icon="user-group" 
            color="green" 
        />
        <x-dashboard-widget 
            title="Active Trucks" 
            :value="$stats['active_trucks']" 
            icon="truck" 
            color="purple" 
        />
        <x-dashboard-widget 
            title="Active ATCs" 
            :value="$stats['active_atcs']" 
            icon="document-text" 
            color="indigo" 
        />
    </div>

    <!-- Financial Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
        <x-dashboard-widget 
            title="Monthly Revenue" 
            :value="'$' . number_format($monthlyRevenue, 2)" 
            icon="currency-dollar" 
            color="green" 
        />
        <x-dashboard-widget 
            title="Monthly Payments" 
            :value="'$' . number_format($monthlyPayments, 2)" 
            icon="banknotes" 
            color="blue" 
        />
        <x-dashboard-widget 
            title="Outstanding Balance" 
            :value="'$' . number_format($outstandingBalance, 2)" 
            icon="exclamation-triangle" 
            color="red" 
        />
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Recent Transactions
                </h3>
                @if($recentTransactions->count() > 0)
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentTransactions as $transaction)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <flux:icon name="clipboard-document-list" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $transaction->customer->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $transaction->driver->name }} • {{ $transaction->atc->atc_number }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-sm text-gray-500 dark:text-gray-400">
                                            ${{ number_format($transaction->total_cost, 2) }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <x-empty-state 
                        icon="clipboard-document-list"
                        title="No Recent Transactions"
                        description="No transactions have been recorded recently."
                    />
                @endif
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Recent Payments
                </h3>
                @if($recentPayments->count() > 0)
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentPayments as $payment)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                                <flux:icon name="banknotes" class="h-4 w-4 text-green-600 dark:text-green-400" />
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $payment->customer->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $payment->payment_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-sm text-gray-500 dark:text-gray-400">
                                            ${{ number_format($payment->amount, 2) }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <x-empty-state 
                        icon="banknotes"
                        title="No Recent Payments"
                        description="No payments have been recorded recently."
                    />
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                Recent Activity
            </h3>
            @if($recentAudits->count() > 0)
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentAudits as $audit)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <flux:icon name="user" class="h-4 w-4 text-gray-600 dark:text-gray-400" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $audit->description }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $audit->user_name }} • {{ $audit->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $audit->formatted_action }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <x-empty-state 
                    icon="clock"
                    title="No Recent Activity"
                    description="No recent activity has been recorded."
                />
            @endif
        </div>
    </div>
</div>
