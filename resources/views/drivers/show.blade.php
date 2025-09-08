<x-layouts.app title="Driver Details">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard')],
            ['name' => 'Drivers', 'url' => route('drivers.index')],
            ['name' => 'Driver Details', 'url' => null]
        ]" />
    </x-slot>

    <livewire:driver.show :driver="$driver" />
</x-layouts.app>
