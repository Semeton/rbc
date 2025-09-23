<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.min.js" integrity="sha512-n/G+dROKbKL3GVngGWmWfwK0yPctjZQM752diVYnXZtD/48agpUKLIn0xDQL9ydZ91x6BiOmTIFwWjjFi2kEFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard.index') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard.index')" :current="request()->routeIs('dashboard.*')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('Data Management')" class="grid">
                    <flux:navlist.item icon="users" :href="route('customers.index')" :current="request()->routeIs('customers.*')" wire:navigate>{{ __('Customers') }}</flux:navlist.item>
                    <flux:navlist.item icon="user-group" :href="route('drivers.index')" :current="request()->routeIs('drivers.*')" wire:navigate>{{ __('Drivers') }}</flux:navlist.item>
                    <flux:navlist.item icon="truck" :href="route('trucks.index')" :current="request()->routeIs('trucks.*')" wire:navigate>{{ __('Trucks') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('atcs.index')" :current="request()->routeIs('atcs.*')" wire:navigate>{{ __('ATCs') }}</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('User Management')" class="grid">
                    <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                    <flux:navlist.item icon="user-plus" :href="route('users.invite')" :current="request()->routeIs('users.invite')" wire:navigate>{{ __('Invite User') }}</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('Transactions')" class="grid">
                    <flux:navlist.item icon="clipboard-document-list" :href="route('transactions.index')" :current="request()->routeIs('transactions.*')" wire:navigate>{{ __('Daily Transactions') }}</flux:navlist.item>
                    <flux:navlist.item icon="banknotes" :href="route('payments.index')" :current="request()->routeIs('payments.*')" wire:navigate>{{ __('Payments') }}</flux:navlist.item>
                    <flux:navlist.item icon="truck" :href="route('truck-movements.index')" :current="request()->routeIs('truck-movements.*')" wire:navigate>{{ __('Truck Movements') }}</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('Maintenance')" class="grid">
                    <flux:navlist.item icon="wrench-screwdriver" :href="route('maintenance.index')" :current="request()->routeIs('maintenance.*')" wire:navigate>{{ __('Truck Maintenance') }}</flux:navlist.item>
                </flux:navlist.group>

        <flux:navlist.group :heading="__('Reports')" class="grid">
            <flux:navlist.item icon="chart-bar" :href="route('reports.index')" :current="request()->routeIs('reports.index')" wire:navigate>{{ __('All Reports') }}</flux:navlist.item>
            <flux:navlist.item icon="users" :href="route('reports.customer-balance')" :current="request()->routeIs('reports.customer-balance')" wire:navigate>{{ __('Customer Balance') }}</flux:navlist.item>
            <flux:navlist.item icon="exclamation-triangle" :href="route('reports.outstanding-balances')" :current="request()->routeIs('reports.outstanding-balances')" wire:navigate>{{ __('Outstanding Balances') }}</flux:navlist.item>
            <flux:navlist.item icon="calendar" :href="route('reports.monthly-sales')" :current="request()->routeIs('reports.monthly-sales')" wire:navigate>{{ __('Monthly Sales') }}</flux:navlist.item>
            <flux:navlist.item icon="credit-card" :href="route('reports.customer-payment-history')" :current="request()->routeIs('reports.customer-payment-history')" wire:navigate>{{ __('Payment History') }}</flux:navlist.item>
            <flux:navlist.item icon="building-office" :href="route('reports.depot-performance')" :current="request()->routeIs('reports.depot-performance')" wire:navigate>{{ __('Depot Performance') }}</flux:navlist.item>
            <flux:navlist.item icon="user" :href="route('reports.driver-performance')" :current="request()->routeIs('reports.driver-performance')" wire:navigate>{{ __('Driver Performance') }}</flux:navlist.item>
            <flux:navlist.item icon="truck" :href="route('reports.truck-utilization')" :current="request()->routeIs('reports.truck-utilization')" wire:navigate>{{ __('Truck Utilization') }}</flux:navlist.item>
                        <flux:navlist.item icon="wrench-screwdriver" :href="route('reports.truck-maintenance-cost')" :current="request()->routeIs('reports.truck-maintenance-cost')" wire:navigate>{{ __('Truck Maintenance Cost') }}</flux:navlist.item>
                        <flux:navlist.item icon="document-text" :href="route('reports.pending-atc')" :current="request()->routeIs('reports.pending-atc')" wire:navigate>{{ __('Pending ATC') }}</flux:navlist.item>
                        <flux:navlist.item icon="currency-dollar" :href="route('reports.cash-flow')" :current="request()->routeIs('reports.cash-flow')" wire:navigate>{{ __('Cash Flow') }}</flux:navlist.item>
                        <flux:navlist.item icon="calendar" :href="route('reports.daily-activity-summary')" :current="request()->routeIs('reports.daily-activity-summary')" wire:navigate>{{ __('Daily Activity Summary') }}</flux:navlist.item>
                        <flux:navlist.item icon="chart-bar" :href="route('reports.profit-estimate')" :current="request()->routeIs('reports.profit-estimate')" wire:navigate>{{ __('Profit Estimate') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            {{-- <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist> --}}

            <!-- Desktop User Menu -->
            @auth
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="Auth::user()->name ?? 'User'"
                    :initials="Auth::user()->initials() ?? 'U'"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ Auth::user()->initials() ?? 'U' }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ Auth::user()->name ?? 'User' }}</span>
                                    <span class="truncate text-xs">{{ Auth::user()->email ?? 'No email' }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
            @endauth
        </flux:sidebar>

        <!-- Mobile User Menu -->
        @auth
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="Auth::user()->initials() ?? 'U'"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ Auth::user()->initials() ?? 'U' }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ Auth::user()->name ?? 'User' }}</span>
                                    <span class="truncate text-xs">{{ Auth::user()->email ?? 'No email' }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>
        @endauth

        {{ $slot }}

        @fluxScripts
    </body>
</html>
