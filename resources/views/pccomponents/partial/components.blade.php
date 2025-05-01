<div id="catalog-container">
    <div class="mx-auto max-w-2xl lg:max-w-7xl lg:px-8">
        <!-- Переключатели вида -->
        <div class="flex justify-end mb-4 space-x-2">
            <button id="view-grid" class="p-2 rounded {{ $view_type == 'grid' ? 'bg-blue-100 text-blue-600' : 'bg-gray-200' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </button>
            <button id="view-list" class="p-2 rounded {{ $view_type == 'list' ? 'bg-blue-100 text-blue-600' : 'bg-gray-200' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        @if($view_type == 'grid')
            <!-- Grid View -->
            <div id="grid-version" class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-5">
                @include('pccomponents.partial.components_grid', ['components' => $components, 'view_type'=>$view_type])
            </div>
        @else
            <!-- List View -->
            <div id="list-version" class="space-y-4">
                @include('pccomponents.partial.components_list', ['components' => $components, "view_type" => $view_type])
            </div>
        @endif

        <!-- Пагинация -->
        <div class="mt-6 pagination">
            {{ $components->withQueryString()->links() }}
        </div>
    </div>
</div>