@props([
    'paginator',
    'infoKey'  => null,
    'showInfo' => true,
])

@if($paginator->hasPages())
@php
    $current   = $paginator->currentPage();
    $last      = $paginator->lastPage();
    $pages     = collect([$current - 1, $current, $current + 1])
                    ->filter(fn($p) => $p >= 1 && $p <= $last)
                    ->values()->all();
    $showFirst = !in_array(1, $pages, true);
    $showLast  = !in_array($last, $pages, true);
@endphp

<div class="d-flex {{ $showInfo ? 'justify-content-between' : 'justify-content-center' }} align-items-center px-4 py-3 border-top flex-wrap gap-2">

    {{-- Info text --}}
    @if($showInfo && $infoKey)
        <small class="text-muted">
            {{ __($infoKey, ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
        </small>
    @elseif($showInfo)
        <small class="text-muted">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} / {{ $paginator->total() }}</small>
    @endif

    {{-- Controls --}}
    <nav aria-label="Pagination">
        <ul class="pagination pagination-sm mb-0">

            {{-- Prev --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}" rel="prev" aria-label="Anterior">
                    <i class="fa-solid fa-chevron-left" style="font-size:11px;"></i>
                </a>
            </li>

            {{-- First page --}}
            @if($showFirst)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if(!in_array(2, $pages, true))
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
            @endif

            {{-- Numeric pages --}}
            @foreach($pages as $page)
                <li class="page-item {{ $page === $current ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endforeach

            {{-- Last page --}}
            @if($showLast)
                @if(!in_array($last - 1, $pages, true))
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($last) }}">{{ $last }}</a>
                </li>
            @endif

            {{-- Next --}}
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}" rel="next" aria-label="Siguiente">
                    <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
                </a>
            </li>

        </ul>
    </nav>

</div>
@endif
