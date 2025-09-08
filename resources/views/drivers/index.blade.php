<x-layouts.app title="Drivers">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard')],
            ['name' => 'Drivers', 'url' => null]
        ]" />
    </x-slot>

    <livewire:driver.index />
</x-layouts.app>
