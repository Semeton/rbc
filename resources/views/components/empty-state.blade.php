@props(['icon' => 'document-text', 'title' => 'No Data', 'description' => 'There are no items to display.', 'action' => null, 'actionText' => 'Add New'])

<div class="text-center py-12">
    <div class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600">
        <flux:icon :name="$icon" class="h-12 w-12" />
    </div>
    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @if($action)
        <div class="mt-6">
            <flux:button variant="primary" :href="$action">
                {{ $actionText }}
            </flux:button>
        </div>
    @endif
</div>
