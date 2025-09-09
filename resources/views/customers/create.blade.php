<x-layouts.app title="Create Customer">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Customers', 'url' => route('customers.index')],
            ['name' => 'Create Customer', 'url' => null]
        ]" />
    </x-slot>

    <livewire:customer.create />
</x-layouts.app>