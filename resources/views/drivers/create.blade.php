<x-layouts.app title="Create Driver">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Drivers', 'url' => route('drivers.index')],
            ['name' => 'Create Driver', 'url' => null]
        ]" />
    </x-slot>

    <livewire:driver.create />
</x-layouts.app>
