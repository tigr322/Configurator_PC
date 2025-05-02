<div id="component-container">
@if ($view === 'grid')
    <div id="grid-version" class="mx-auto max-w-2xl lg:max-w-7xl lg:px-8">
        <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-5">
            @each('pccomponents.partial.components_grid', $components, 'component')
        </div>
    </div>
@else
    <div id="list-version" class="space-y-4">
        @each('pccomponents.partial.components_list', $components, 'component')
    </div>
@endif

<div id="pagination-wrapper" class="mt-6">
    {{ $components->withQueryString()->links() }}
</div>
</div>
