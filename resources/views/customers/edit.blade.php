<x-layouts.app title="Edit Customer">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Customers', 'url' => route('customers.index')],
            ['name' => 'Edit Customer', 'url' => null]
        ]" />
    </x-slot>

    <livewire:customer.edit :customer="$customer" />
</x-layouts.app>