@props(['type' => 'info', 'title' => '', 'message' => '', 'dismissible' => true])

@php
    $typeClasses = [
        'success' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900 dark:border-green-700 dark:text-green-200',
        'error' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900 dark:border-red-700 dark:text-red-200',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-200',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200',
    ];
    
    $icons = [
        'success' => 'check-circle',
        'error' => 'x-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'information-circle',
    ];
@endphp

<div class="rounded-md border p-4 {{ $typeClasses[$type] }}" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex">
        <div class="flex-shrink-0">
            <flux:icon :name="$icons[$type]" class="h-5 w-5" />
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium">{{ $title }}</h3>
            @endif
            @if($message)
                <div class="mt-1 text-sm">
                    {{ $message }}
                </div>
            @endif
            {{ $slot }}
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2">
                        <span class="sr-only">Dismiss</span>
                        <flux:icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
