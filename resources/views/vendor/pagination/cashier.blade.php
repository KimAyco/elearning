@if ($paginator->hasPages())
    <nav class="cashier-pager" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="cashier-pager-inner">
            @if ($paginator->onFirstPage())
                <span class="cashier-pager-step disabled">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="cashier-pager-step" rel="prev">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="cashier-pager-ellipsis" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="cashier-pager-page is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="cashier-pager-page">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="cashier-pager-step" rel="next">Next</a>
            @else
                <span class="cashier-pager-step disabled">Next</span>
            @endif
        </div>
    </nav>
@endif
