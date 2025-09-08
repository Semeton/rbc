@props(['title' => '', 'value' => '', 'icon' => 'chart-bar', 'trend' => null, 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'yellow' => 'bg-yellow-500',
        'red' => 'bg-red-500',
        'purple' => 'bg-purple-500',
        'indigo' => 'bg-indigo-500',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 {{ $colorClasses[$color] }} rounded-md flex items-center justify-center">
                    <flux:icon :name="$icon" class="h-5 w-5 text-white" />
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $value }}
                        </div>
                        @if($trend)
                            <div class="ml-2 flex items-baseline text-sm font-semibold {{ $trend['positive'] ? 'text-green-600' : 'text-red-600' }}">
                                <flux:icon :name="$trend['positive'] ? 'arrow-trending-up' : 'arrow-trending-down'" class="self-center flex-shrink-0 h-4 w-4" />
                                <span class="sr-only">{{ $trend['positive'] ? 'Increased' : 'Decreased' }} by</span>
                                {{ $trend['value'] }}
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    @if(isset($footer))
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            {{ $footer }}
        </div>
    @endif
</div>
