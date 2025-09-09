<x-layouts.app title="Customers">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Customers', 'url' => null]
        ]" />
    </x-slot>

    <livewire:customer.index />
</x-layouts.app>
