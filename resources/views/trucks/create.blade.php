<x-layouts.app title="Create Truck">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard')],
            ['name' => 'Trucks', 'url' => route('trucks.index')],
            ['name' => 'Create Truck', 'url' => null]
        ]" />
    </x-slot>

    <livewire:truck.create />
</x-layouts.app>
