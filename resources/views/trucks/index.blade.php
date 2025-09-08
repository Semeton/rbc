<x-layouts.app title="Trucks">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard')],
            ['name' => 'Trucks', 'url' => null]
        ]" />
    </x-slot>

    <livewire:truck.index />
</x-layouts.app>
