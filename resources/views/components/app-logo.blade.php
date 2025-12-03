@props(['stacked' => false])

@php
    $brandName = config('app.name', 'QuarryQuarto');
    $brandColor = 'text-[#6B3F1B]';
@endphp

@if ($stacked)
    <div class="flex flex-col items-center gap-2 text-center">
        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F4E8D3]">
            <x-app-logo-icon class="h-9 w-9" />
        </span>
        <span class="text-base font-semibold tracking-tight {{ $brandColor }}">{{ $brandName }}</span>
    </div>
@else
    <div class="flex items-center gap-2">
        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#F4E8D3]">
            <x-app-logo-icon class="h-7 w-7" />
        </span>
        <div class="grid text-sm leading-tight">
            <span class="font-semibold {{ $brandColor }}">{{ $brandName }}</span>
            <span class="text-[10px] uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Logistics OS</span>
        </div>
    </div>
@endif
