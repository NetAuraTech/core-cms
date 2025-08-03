@if ($paginator->hasPages())
    <ul class="flex-group align-items-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="padding-3" aria-disabled="true">
                <span>«</span>
            </li>
        @else
            <li>
                <a class="button padding-3" data-type="paginator" href="{{ $paginator->previousPageUrl() }}"
                   rel="prev">«</a>
            </li>
        @endif
        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li aria-disabled="true"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="button is-active padding-3" data-type="primary" aria-current="page">
                            <span>{{ $page }}</span></li>
                    @else
                        <li><a class="button padding-3" data-type="paginator" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a class="button padding-3" data-type="paginator" href="{{ $paginator->nextPageUrl() }}"
                   rel="next">»</a>
            </li>
        @else
            <li class="padding-3" aria-disabled="true">
                <span>»</span>
            </li>
        @endif
    </ul>
@endif
