<x-layouts.app title="Daily Transactions">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Daily Transactions', 'url' => null]
        ]" />
    </x-slot>

    <livewire:transaction.index />
</x-layouts.app>
