<div class="card-body overflow-auto ml-3 h-full">
    <div class="row">
        @if(session('success'))
    <div class="alert alert-success bg-green-500">
        {{ session('success') }}
    </div>
    @endif
        @php
            $subUserData = json_decode($subUserData);
        @endphp
        {{-- @dd($subUserData) --}}
        <div class="col-12">
            <div class="card">
                <div class="flex items-center justify-between bg-white dark:bg-gray-900">

                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative m-2">
                        <div class="flex pointer-events-none absolute inset-y-0 left-0 items-center pl-3">
                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" id="table-search-users"
                            class="block w-80 rounded-lg border border-gray-300 bg-gray-300 p-2 pl-10 text-base text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                            placeholder="Search" />
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0 whitespace-nowrap" style="height: 300px;">
                    <table
                        class="border dark:border-gray-600 w-full text-base text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-base text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th>#</th>
                                <th>SUB USER NAME</th>
                                <th>COMPANY NAME</th>
                                <th>USER NAME</th>
                                <th>EMAIL</th>
                                <th>PHONE</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subUserData as $key=> $data)
                                <tr class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                                    <td><b>{{ ++$key }}</b></td>
                                    <td>{{ $data->team_user_name ?? ''}}</td>
                                    <td> {{ $data->user->company_name  ?? 'N/A'}} </td>
                                    <td> {{ $data->user->name ?? '' }} </td>
                                    <td> {{ $data->email ?? 'N/A' }} </td>
                                    <td> {{ $data->phone ?? '' }} </td>
                                    
                                   
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 
</div>
