{{--
  Params:
  - $title (string)
  - $subtitle (string|null)
  - $crumbs (array of ['label' => string, 'url' => string|null])
  - $actions (slot optional via $slot)
--}}
@props([
    'title',
    'subtitle' => null,
    'crumbs' => [],
])

<div class="space-y-4">
    @if(count($crumbs))
        <nav class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500" aria-label="Breadcrumb">
            @foreach($crumbs as $i => $crumb)
                @if($i > 0)
                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
                @if(!empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}" class="hover:text-blue-700 transition-colors">{{ $crumb['label'] }}</a>
                @else
                    <span class="text-gray-800 font-medium">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $title }}</h1>
            @if($subtitle)
                <p class="mt-1.5 text-gray-600 max-w-2xl">{{ $subtitle }}</p>
            @endif
        </div>
        @if(trim($slot ?? '') !== '')
            <div class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-2 w-full lg:w-auto shrink-0">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
