@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5>Artist Expertize</h5>
            <a href="{{ route('CreateArtistExpertize') }}" class="btn rounded-pill btn-primary">Create</a>
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
                    @foreach($data as $index=>$item)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong> {{ $index+1 }} </strong></td>
                        <td>{{ $item->name }}</td>
                        <td>
                            {{ Str::length($item->description) > 20 ? substr($item->description, 0,15) . '...' : $item->description }}
                        </td>
                        <td>
                            @if($item->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>
                        </td>
                        @else
                        <span class="badge bg-label-warning me-1">InActive</span></td>
                        @endif

                        <td>
                            <a href="{{ route('ShowArtistExpertize', [ 'id' => $item->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                            <a href="{{ route('EditArtistExpertize', [ 'id' => $item->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteArtistExpertize', ['id' => $item->id]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                        </td>
                    </tr>
                    @endforeach
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
@endsection('SuperAdminContent')