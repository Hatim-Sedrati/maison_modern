@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-wrap items-center justify-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-xs uppercase tracking-widest text-muted">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-xs uppercase tracking-widest hover:text-muted" rel="prev">Previous</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-muted">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="min-h-11 min-w-11 border border-charcoal px-3 py-2 text-center text-sm">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="min-h-11 min-w-11 px-3 py-2 text-center text-sm text-charcoal-light hover:text-charcoal">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-xs uppercase tracking-widest hover:text-muted" rel="next">Next</a>
        @else
            <span class="px-3 py-2 text-xs uppercase tracking-widest text-muted">Next</span>
        @endif
    </nav>
@endif
