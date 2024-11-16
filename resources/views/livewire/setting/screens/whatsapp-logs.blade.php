<div>
    <div
    class="relative flex flex-col w-full h-full overflow-scroll text-gray-700 bg-white shadow-md bg-clip-border rounded-xl">
    <table class="w-full text-left table-auto min-w-max">
      <thead>
        <tr>
            <th class="p-2 border-b border-blue-gray-100 bg-blue-gray-50">
                <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                  #
                </p>
              </th>
          <th class="p-2 border-b border-blue-gray-100 bg-blue-gray-50">
            <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
              Receiver
            </p>
          </th>
          <th class="p-2 border-b border-blue-gray-100 bg-blue-gray-50">
            <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
              Amount Deducted
            </p>
          </th>
          <th class="p-2 border-b border-blue-gray-100 bg-blue-gray-50">
            <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
              Remaining Balance
            </p>
          </th>
          <th class="p-2 border-b border-blue-gray-100 bg-blue-gray-50">
            <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
              Action
            </p>
          </th>
          <th class="p-2 border-b border-blue-gray-100 bg-blue-gray-50">
            <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
              Date and Time
            </p>
          </th>
         
        </tr>
      </thead>
      <tbody>
        @foreach ($whatsappLogs as $key => $logs )
            
        
        <tr class="even:bg-blue-gray-50/50">
            <td class="p-2">
                <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                  {{$key + 1}}
                </p>
              </td>
          <td class="p-2">
            <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
              {{$logs->recipient}}
            </p>
          </td>
          <td class="p-2">
            <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
             {{$logs->amount_deducted}}
            </p>
          </td>
          <td class="p-2">
            <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
              {{$logs->remaining_balance}}
            </p>
          </td>
          <td class="p-2">
            <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                {{ Str::of($logs->action)->replace('_', ' ')->title() }}
            </p>
          </td>
          <td class="p-2">
            <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
              {{ date('d-m-Y H:i:s', strtotime($logs->created_at))}}
            </p>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
