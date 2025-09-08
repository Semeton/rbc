@props(['loading' => false, 'text' => 'Loading...'])

@if($loading)
    <div class="flex items-center justify-center p-8">
        <div class="flex items-center space-x-2">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <span class="text-gray-600 dark:text-gray-400">{{ $text }}</span>
        </div>
    </div>
@else
    {{ $slot }}
@endif
