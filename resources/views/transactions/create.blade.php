<x-layouts.app title="Create Transaction">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Transactions', 'url' => route('transactions.index')],
            ['name' => 'Create Transaction', 'url' => null]
        ]" />
    </x-slot>

    <livewire:transaction.create />
</x-layouts.app>