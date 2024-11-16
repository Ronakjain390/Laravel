<div>
    <h2 class="text-2xl font-bold mb-4 text-black flex justify-center mt-3">Logs</h2>

    {{-- <div class="mb-4 max-w-xs text-xs">
        <input wire:model.debounce.300ms="search" type="text" placeholder="Search logs..." class="w-full px-4 py-2 border rounded-md">
    </div> --}}

    @if ($errors->has('download'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ $errors->first('download') }}</span>
        </div>
    @endif

    <table class="w-full border-collapse border text-xs text-black">
        <thead>
            <tr class="bg-gray-100">

                <th class="border p-2 text-left">#</th>
                <th class="border p-2 text-left">File Name</th>
                {{-- <th class="border p-2 text-left">File Type</th> --}}
                <th class="border p-2 text-left">Status</th>
                <th class="border p-2 text-left">Created At</th>
                {{-- <th class="border p-2 text-left">Error Message</th> --}}
                {{-- <th class="border p-2 text-left">Actions</th> --}}
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $key => $log)
                <tr>
                    <td class="border p-2">{{ $key + 1 }}</td>
                    <td class="border p-2">{{ $log->file_name }}</td>
                    {{-- <td class="border p-2">{{ $log->file_type }}</td> --}}
                    <td class="border p-2">
                        <span class="px-2 py-1 rounded-full text-sm
                            @if($log->status === 'completed') bg-green-200 text-green-800
                            @elseif($log->status === 'processing') bg-yellow-200 text-yellow-800
                            @else bg-red-200 text-red-800
                            @endif">
                            {{ ucfirst($log->status) }}
                        </span>
                    </td>
                    <td class="border p-2">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    {{-- <td class="border p-2">{{ $log->error_message ?? 'N/A' }}</td> --}}
                    {{-- <td class="border p-2">
                        <button wire:click="downloadFile({{ $log->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Download
                        </button>
                    </td> --}}
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border p-2 text-center">No logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
