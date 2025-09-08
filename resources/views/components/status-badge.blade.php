@props(['status' => true, 'activeText' => 'Active', 'inactiveText' => 'Inactive'])

@if($status)
    <flux:badge variant="success">{{ $activeText }}</flux:badge>
@else
    <flux:badge variant="danger">{{ $inactiveText }}</flux:badge>
@endif
