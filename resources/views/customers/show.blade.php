<x-layouts.app title="Customer Details">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard')],
            ['name' => 'Customers', 'url' => route('customers.index')],
            ['name' => 'Customer Details', 'url' => null]
        ]" />
    </x-slot>

    <livewire:customer.show :customer="$customer" />
</x-layouts.app>