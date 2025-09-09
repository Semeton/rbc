<x-layouts.app title="Truck Details">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Trucks', 'url' => route('trucks.index')],
            ['name' => 'Truck Details', 'url' => null]
        ]" />
    </x-slot>

    <livewire:truck.show :truck="$truck" />
</x-layouts.app>
