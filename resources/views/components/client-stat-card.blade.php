@props([
    'title',
    'value',
    'subtitle' => null,
    'theme' => 'slate',
    'compact' => false,
    'icon' => 'users',
])

@php
    $themes = [
        'slate' => [
            'title' => 'text-slate-500',
            'subtitle' => 'text-slate-500',
            'blob' => 'bg-slate-200/60',
            'iconWrap' => 'bg-slate-100',
            'icon' => 'text-slate-500',
        ],
        'green' => [
            'title' => 'text-emerald-600',
            'subtitle' => 'text-emerald-600',
            'blob' => 'bg-emerald-200/50',
            'iconWrap' => 'bg-emerald-50',
            'icon' => 'text-emerald-500',
        ],
        'blue' => [
            'title' => 'text-blue-600',
            'subtitle' => 'text-blue-600',
            'blob' => 'bg-blue-200/50',
            'iconWrap' => 'bg-blue-50',
            'icon' => 'text-blue-500',
        ],
        'amber' => [
            'title' => 'text-amber-600',
            'subtitle' => 'text-amber-600',
            'blob' => 'bg-amber-200/50',
            'iconWrap' => 'bg-amber-50',
            'icon' => 'text-amber-500',
        ],
        'yellow' => [
            'title' => 'text-yellow-600',
            'subtitle' => 'text-yellow-600',
            'blob' => 'bg-yellow-200/50',
            'iconWrap' => 'bg-yellow-50',
            'icon' => 'text-yellow-500',
        ],
        'red' => [
            'title' => 'text-red-500',
            'subtitle' => 'text-red-500',
            'blob' => 'bg-red-200/50',
            'iconWrap' => 'bg-red-50',
            'icon' => 'text-red-500',
        ],
        'teal' => [
            'title' => 'text-teal-600',
            'subtitle' => 'text-teal-600',
            'blob' => 'bg-teal-200/50',
            'iconWrap' => 'bg-teal-50',
            'icon' => 'text-teal-500',
        ],
        'purple' => [
            'title' => 'text-purple-600',
            'subtitle' => 'text-purple-600',
            'blob' => 'bg-purple-200/50',
            'iconWrap' => 'bg-purple-50',
            'icon' => 'text-purple-500',
        ],
        'orange' => [
            'title' => 'text-orange-600',
            'subtitle' => 'text-orange-600',
            'blob' => 'bg-orange-200/50',
            'iconWrap' => 'bg-orange-50',
            'icon' => 'text-orange-500',
        ],
    ];

    $styles = $themes[$theme] ?? $themes['slate'];
    $padding = $compact ? 'p-6' : 'p-6';
    $valueSize = $compact ? 'text-2xl' : 'text-3xl';
    $blobSize = $compact ? 'w-24 h-24 -top-4 -right-4' : 'w-32 h-32 md:w-36 md:h-36 -top-6 -right-6';
    $iconSize = $compact ? 'w-9 h-9' : 'w-10 h-10';
    $svgSize = $compact ? 'w-4 h-4' : 'w-5 h-5';
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100 {$padding}"]) }}>
    <div class="absolute {{ $blobSize }} rounded-full {{ $styles['blob'] }} pointer-events-none"></div>

    <div class="relative flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium {{ $styles['title'] }}">
                {{ $title }}
            </p>
            <p class="{{ $valueSize }} font-bold text-gray-900 mt-4 leading-tight break-words">
                {{ $value }}
            </p>
            @if($subtitle)
                <p class="text-sm font-medium {{ $styles['subtitle'] }} mt-1">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="relative shrink-0 flex {{ $iconSize }} items-center justify-center rounded-full {{ $styles['iconWrap'] }} {{ $styles['icon'] }}">
            @switch($icon)
                @case('users')
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    @break
                @case('trending')
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    @break
                @case('wallet')
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    @break
                @case('clock')
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @break
                @case('x-circle')
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @break
                @case('ticket')
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    @break
                @case('message')
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    @break
                @default
                    <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
            @endswitch
        </div>
    </div>
</div>
