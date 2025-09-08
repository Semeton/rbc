@props(['status' => 'active', 'activeText' => 'Active', 'inactiveText' => 'Inactive'])

@if($status === 'active')
    <flux:badge variant="success">{{ $activeText }}</flux:badge>
@else
    <flux:badge variant="danger">{{ $inactiveText }}</flux:badge>
@endif
