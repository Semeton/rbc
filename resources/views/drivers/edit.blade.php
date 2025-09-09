<x-layouts.app title="Edit Driver">
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['name' => 'Dashboard', 'url' => route('dashboard.index')],
            ['name' => 'Drivers', 'url' => route('drivers.index')],
            ['name' => 'Edit Driver', 'url' => null]
        ]" />
    </x-slot>

    <livewire:driver.edit :driver="$driver" />
</x-layouts.app>
