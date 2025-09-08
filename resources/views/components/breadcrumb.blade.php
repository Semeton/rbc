@props(['items' => []])

@if(count($items) > 0)
    <flux:breadcrumbs class="mb-6">
        @foreach($items as $index => $item)
            @if($index === count($items) - 1)
                <flux:breadcrumbs.item current>{{ $item['name'] }}</flux:breadcrumbs.item>
            @else
                <flux:breadcrumbs.item :href="$item['url'] ?? '#'">{{ $item['name'] }}</flux:breadcrumbs.item>
            @endif
        @endforeach
    </flux:breadcrumbs>
@endif
