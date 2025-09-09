@props(['headers' => [], 'data' => [], 'actions' => null, 'emptyMessage' => 'No data available'])

<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-zinc-900">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
                @if($actions)
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($data as $row)
                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                    @foreach($row as $cell)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $cell }}
                        </td>
                    @endforeach
                    @if($actions)
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            {{ $actions($row) }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
