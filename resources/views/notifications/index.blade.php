<x-app-layout>
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'href' => route('dashboard.index')],
        ['name' => 'Notifications', 'href' => route('notifications.index')]
    ]" />

    <livewire:notification.index />
</x-app-layout>
