<div>
    <h1 class="">
        @include('components.assets.tableComponent.table')
        @foreach ($receivedPanelColumnDisplayNames as $columnName)
            <th class="px-2 py-2 font-normal">{{ $columnName }}</th>
        @endforeach
    </h1>
</div>
