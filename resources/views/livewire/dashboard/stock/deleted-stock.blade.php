<div class="relative overflow-x-auto shadow-md sm:rounded-lg"
    @php
        $mainUser = json_decode($this->mainUser);
        @endphp
        <div x-data="checkboxes()" x-init="initCheckboxes({{ $deletedProducts->pluck('id') }})" class="table-container overflow-x-auto">
            <div>

            <div class="fixed-header-container">
                {{-- <div class="filters flex items-center mb-5 p-2 space-x-2 sticky top-0 bg-white z-10">
                    <!-- ... (keep your existing filter code) ... -->
                </div> --}}
            <table class="w-full divide-y divide-gray-200" x-data="{ showSelected: false }">
                <thead class="bg-gray-50 sticky top-0 whitespace-nowrap">
                    <tr>
                        {{-- <th class="px-1 py-1 text-left text-xs font-medium text-black uppercase tracking-wider">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox" @click="showSelected = !showSelected" x-on:click="toggleAll" x-bind:checked="allChecked">
                            </label>
                        </th> --}}
                        <th x-show="!showSelected" scope="col" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">#</th>
                        @foreach ($ColumnDisplayNames as $index => $columnName)
                        @if ($index < 3)
                        <th x-show="!showSelected" scope="col" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $columnName }}</th>
                        @endif
                        @endforeach
                        @if (!in_array('Article', $ColumnDisplayNames))
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Article</th>
                        @endif

                        @if (!in_array('hsn' || 'Hsn', $ColumnDisplayNames))
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">HSN</th>
                        @endif
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Item Code</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Category</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Warehouse</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Location</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Unit</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Quantity</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Rate</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Date</th>
                        <th x-show="!showSelected" class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">Time</th>


                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($deletedProducts as $key => $product)
                    <tr>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{$key + 1}}</td>
                        @foreach($product->details as $index => $detail)
                        @if ($index < 3)
                            <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::lower($detail->column_value), 20) }}</td>
                            @endif
                        @endforeach

                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->item_code }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->category ?? '' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->warehouse ?? '' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ ucfirst($product->location) }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ ucfirst($product->unit) }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->qty }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->rate }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->created_at->format('j-m-Y') }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->created_at->format('h:i A') }}</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>



        </div>
        {{ $deletedProducts->links() }}
    </div>
</div>
<style>
    .fixed-header-container {
        height: calc(100vh - 190px); /* Adjust this value based on your layout */
        overflow-y: hidden;
    }

    .table-container {
        overflow-y: auto;
        max-height: calc(100vh - 300px); /* Adjust this value based on your layout */
    }

    thead {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: white;
    }

    tbody {
        overflow-y: auto;
    }
    .fixed-header-container {
        height: calc(100vh - 200px); /* Adjust this value based on your layout */
        overflow-y: auto;
    }


    th, td {
        min-width: 100px; /* Adjust this value as needed */
        max-width: 200px; /* Adjust this value as needed */
        overflow: hidden;
        text-overflow: ellipsis;
    }

</style>
