@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Event Ticket</h5>
                    <a href="{{ route('EventTicket') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateEventTicket',$eventTicketData->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-categoryname">Ticket Name</label>
                            <input type="text" class="form-control" id="basic-default-categoryname" value="{{ $eventTicketData->ticket_name }}" name="ticket_name" placeholder="Ticket Name"  />
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-image">Total Tickets</label>
                            <input type="text" class="form-control" id="basic-default-image" value="{{ $eventTicketData->total_tickets }}" name="total_tickets" placeholder="Total Tickets" />
                        </div>
                        </div>
                        <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-image">Coupons</label>
                            <input type="text" class="form-control" id="basic-default-image" value="{{ $eventTicketData->coupons }}" name="coupons" placeholder="coupons" />
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-price">Price</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">₹</span>
                                <input type="text" class="form-control" value="{{ $eventTicketData->price }}" name="price" placeholder="100" aria-label="Amount" />
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label for="html5-date-input" class="col-md-2 col-form-label">Start Date</label>
                                <input class="form-control" type="date" value="{{ $eventTicketData->start_date }}" name="start_date" id="html5-date-input" />
                            </div>
                            <div class="col-sm-6">
                                <label for="html5-date-input" class="col-md-2 col-form-label">End Date</label>
                                <input class="form-control" type="date" value="{{ $eventTicketData->end_date }}" id="html5-date-input" name="end_date" />
                            </div>
                        </div>
                        <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-image">Age</label>
                            <input type="number" class="form-control" id="basic-default-image" value="{{ $eventTicketData->age }}" name="age" placeholder="Age" />
                        </div>
                       
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $eventTicketData->status=='1' ? 'checked':'' }} />
                                <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')