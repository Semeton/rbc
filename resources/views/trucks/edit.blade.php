<x-layouts.app title="Edit Truck">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Trucks', 'url' => route('trucks.index')],
            ['name' => 'Edit Truck', 'url' => null]
        ]" />
    </x-slot>

    <livewire:truck.edit :truck="$truck" />
</x-layouts.app>
