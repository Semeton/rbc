<x-app-layout>
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'href' => route('dashboard.index')],
        ['name' => 'Transactions', 'href' => route('transactions.index')],
        ['name' => 'Create Transaction', 'href' => route('transactions.create')]
    ]" />

    <livewire:transaction.create />
</x-app-layout>