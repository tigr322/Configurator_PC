@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center space-x-1 mt-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-sm bg-gray-200 text-gray-500 rounded cursor-not-allowed">&laquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1 text-sm bg-white border border-gray-300 text-gray-700 hover:bg-blue-100 rounded"
               aria-label="@lang('pagination.previous')">
                &laquo;
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-1 text-sm bg-gray-100 text-gray-500 rounded">{{ $element }}</span>
            @endif

            {{-- Array of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 text-sm bg-blue-500 text-white rounded font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1 text-sm bg-white border border-gray-300 text-gray-700 hover:bg-blue-100 rounded"
                           aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1 text-sm bg-white border border-gray-300 text-gray-700 hover:bg-blue-100 rounded"
               aria-label="@lang('pagination.next')">
                &raquo;
            </a>
        @else
            <span class="px-3 py-1 text-sm bg-gray-200 text-gray-500 rounded cursor-not-allowed">&raquo;</span>
        @endif
    </nav>
@endif
