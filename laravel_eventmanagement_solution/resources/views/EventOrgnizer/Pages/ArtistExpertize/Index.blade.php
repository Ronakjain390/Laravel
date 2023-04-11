@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h4>Artist Expertizes</h4>
            <a href="{{ route('CreateOrganizerArtistExpertize') }}" class="btn rounded-pill btn-primary">Create</a>
        </div>
        @if(Session::has('success'))
        <script>
            toastr.success("{{ Session::get('success') }}")
        </script>
        @endif
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(!$data->isEmpty())
                    @foreach($data as $index=>$item)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong> {{ $index+1 }} </strong></td>
                        <td>{{ $item->name }}</td>
                        <td>
                            {!! Str::length($item->description) > 20 ? substr($item->description, 0,15) . '...' : $item->description !!}
                        </td>
                        <td>
                            @if($item->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>
                        </td>
                        @else
                        <span class="badge bg-label-warning me-1">InActive</span></td>
                        @endif

                        <td>
                            <a href="{{ route('ShowOrganizerArtistExpertize', [ 'id' => $item->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                            <a href="{{ route('EditOrganizerArtistExpertize', [ 'id' => $item->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteOrganizerArtistExpertize', ['id' => $item->id]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="12" style="text-align: center;">
                            No Data Present, please create
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@if(Session::has('success'))
<script>
    toastr.success("{{ Session::get('success') }}")
</script>
@endif
@endsection('content')