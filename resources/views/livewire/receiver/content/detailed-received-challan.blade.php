<div>
    <div class="flex justify-end">
        <a href="javascript:history.back()" class="border border-gray-900 dark:border-white dark:focus:bg-gray-700 dark:hover:bg-gray-700 dark:hover:text-white dark:text-white focus:bg-gray-900 focus:ring-2 focus:ring-gray-500 focus:text-white focus:z-10 mb-2 mr-2 px-4 py-1 rounded-r-lg rounded-lg text-black text-sm">Back</a>
    </div>
    @php
    $mainUser = json_decode($mainUser);
    @endphp
      <table  class="w-full text-xs text-left text-gray-500 dark:text-gray-400" x-data="{
        showSelected: false,
        selectedCount: 0
        }">
        <thead  class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
            <div x-show="selectedCount > 0" >

                @include('components.assets.tableComponent.th')
            </div>
        </thead>
        <tbody class="text-black">
            @foreach ($detailedChallans as $key => $columnName)
            {{-- @dump($columnName) --}}
            @php
            $columnName = (object) $columnName;
                // $columnName->statuses[0] = (object) $columnName->statuses[0];
                $mainUser = json_decode($this->mainUser);
                $panelName = strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']));
                // dd($columnName);
            @endphp
            <tr
                class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                    {{ ++$key }}</div>
                </td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>


                @foreach ($columnName->orderDetails as $keys => $details)
                @php
                $details = (object) $details;
                @endphp

                    @foreach ($details->columns as $index => $column)
                    @php
                    $column = (object) $column;
                    @endphp
                        @if ($index < 3)
                            <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $column->column_value }}</td>
                        @endif
                    @endforeach


                    <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->unit }}</td>
                    <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->qty }}</td>
                    <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->rate }}</td>
                    <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->total_amount }}</td>
            <tr>
                @if(count($columnName->orderDetails) > 1 && $keys < count($columnName->orderDetails) - 1)
                    <td></td>
                    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
                    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>
                    @endif

                @endforeach

            </tr>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{$detailedChallans->links()}}
</div>
