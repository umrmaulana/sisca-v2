@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
        <div class="flex items-center justify-center">
            <span class="relative z-0 inline-flex shadow-sm rounded-md">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 cursor-default rounded-l-md leading-5"
                            aria-hidden="true">
                            <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                        aria-label="{{ __('pagination.previous') }}">
                        <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $maxPages = 3;
                    $start = max(1, $currentPage - floor($maxPages / 2));
                    $end = min($lastPage, $start + $maxPages - 1);

                    // Adjust start if we're near the end
                    if ($end - $start + 1 < $maxPages) {
                        $start = max(1, $end - $maxPages + 1);
                    }
                @endphp

                @if ($start > 1)
                    <a href="{{ $paginator->url(1) }}"
                        class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                        aria-label="{{ __('Go to page :page', ['page' => 1]) }}">
                        1
                    </a>
                    @if ($start > 2)
                        <span aria-disabled="true">
                            <span
                                class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 cursor-default leading-5">...</span>
                        </span>
                    @endif
                @endif

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $currentPage)
                        <span aria-current="page">
                            <span
                                class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-blue-600 border border-blue-600 cursor-default leading-5">{{ $page }}</span>
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}"
                            class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)
                        <span aria-disabled="true">
                            <span
                                class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 cursor-default leading-5">...</span>
                        </span>
                    @endif
                    <a href="{{ $paginator->url($lastPage) }}"
                        class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                        aria-label="{{ __('Go to page :page', ['page' => $lastPage]) }}">
                        {{ $lastPage }}
                    </a>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                        aria-label="{{ __('pagination.next') }}">
                        <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 cursor-default rounded-r-md leading-5"
                            aria-hidden="true">
                            <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
