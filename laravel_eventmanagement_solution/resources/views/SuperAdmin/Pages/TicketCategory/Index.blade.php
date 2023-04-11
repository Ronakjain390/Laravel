@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Ticket Category</h5>
            <a href="{{ route('CreateTicketCategory') }}" class="btn rounded-pill btn-primary">Create</a>
        </div>
       
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
                    @foreach($ticketCategoryData as $index=>$ticketCategoryData)
                    <tr>
                        <td> {{ $index+1 }}<strong></strong></td>

                        <td>
                            <div style="display: flex;">{{ $ticketCategoryData->name }} </div>
                        </td>
                        <td>
                            @if(Str::length($ticketCategoryData->description) > 20)
                            {!! substr($ticketCategoryData->description, 0,15) . '...' !!}
                            @else
                            {!! $ticketCategoryData->description !!}
                            @endif
                        </td>
                        <td>
                            @if($ticketCategoryData->status == 1)
                            <span class="badge bg-label-primary me-1">Active</span>
                            @else
                            <span class="badge bg-label-warning me-1">InActive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('ShowTicketCategory', ['id' => $ticketCategoryData->id ]) }}"><i class="bx bx-show-alt me-1"></i></a>
                            <a href="{{ route('EditTicketCategory', ['id' => $ticketCategoryData->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteTicketCategory', ['id' => $ticketCategoryData->id ]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
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