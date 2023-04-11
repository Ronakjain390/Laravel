@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Events Category</h5>
            <a href="{{ route('EventTicket') }}" class="btn rounded-pill btn-primary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">


                <div class="col-sm-6">
                    <label><b>Category Name</b></label>
                    <div>
                        {{ $eventTicketData->ticket_name }}
                    </div>
                </div>

                <div class="col-sm-6">
                    <b> Total TIckets </b>
                    <div>{{ $eventTicketData->total_tickets }}</div>
                </div>
                <div class="col-sm-6">
                    <b> Coupons </b>
                    <div>{{ $eventTicketData->coupons }}</div>
                </div>
                <div class="col-sm-6">
                    <b> Start Date </b>
                    <div>{{ $eventTicketData->start_date }}</div>
                </div>
                <div class="col-sm-6">
                    <b> End Date </b>
                    <div>{{ $eventTicketData->end_date }}</div>
                </div>
                <div class="col-sm-6">
                    <b> Age </b>
                    <div>{{ $eventTicketData->age }}</div>
                </div>
                <div class="col-sm-6">
                    <b> Price </b>
                    <div>{{ $eventTicketData->price }}</div>
                </div>
            </div> 
            <div class="col-sm-6">
                <label><b>Status</b></label>
                <div>
                    @if($eventTicketData->status ==1)
                    <span class="badge bg-label-primary me-1">ACTIVE</span>
                    @else
                    <span class="badge bg-label-warning me-1">INACTIVE</span>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection('SuperAdminContent')