@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Tickets Category Detail</h5>
            <a href="{{ route('TicketCategory') }}" class="btn rounded-pill btn-primary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <label><b>Category Name</b></label>
                    <div>
                        {{ $ticketCategory->name }}
                    </div>
                </div>
                <div class="col-md-6">
                    <label><b>Status</b></label>
                    <div>
                        @if($ticketCategory->status ==1)
                        <span class="badge bg-label-primary me-1">Active</span>
                        @else
                        <span class="badge bg-label-warning me-1">InActive</span>
                        @endif
                    </div>

                </div>
                <div class="col-sm-12 mt-4">
                    <b> Description </b>
                    <div>{!! $ticketCategory->description !!}</div>
                </div>
            </div>

            <div class="col-sm-6 mt-4">
                <b> Image </b>
                <div class="img-fluid">
                    @if($ticketCategory->image == '')
                    <img src="{{ asset('Assets/Defaultimage/Default_ticket.png') }}" class="img-thumbnail" alt="No Image" width="150" height="100" />
                    @else
                    <img src="{{ asset('Assets/images/' .$ticketCategory->image ) }}" class="img-thumbnail" alt="no Image" width="150" height="100" />
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection('SuperAdminContent')

<style>
    img.img-thumbnail {
        height: 150px;
        width: 50%;
    }
</style>