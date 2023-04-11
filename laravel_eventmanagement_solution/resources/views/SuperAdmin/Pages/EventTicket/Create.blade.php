@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Events Tickets </h5>
                    <a href="{{ route('EventTicket') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                @if (Session::has('success'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('StoreEventTicket') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-categoryname">Ticket Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="basic-default-categoryname" name="ticket_name" placeholder="Ticket Name" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Total Tickets <span class="required">*</span></label>
                                <input type="text" class="form-control" id="basic-default-image" name="total_tickets" placeholder="Total Tickets" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Coupons</label>
                                <input type="text" class="form-control" id="basic-default-image" name="coupons" placeholder="coupons" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-price">Price <span class="required">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">₹</span>
                                    <input type="text" class="form-control" name="price" placeholder="100" aria-label="Amount" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label for="html5-date-input" class="col-md-2 col-form-label">Start Date <span class="required">*</span></label>
                                <input class="form-control" type="date" name="start_date" id="html5-date-input" />
                            </div>
                            <div class="col-sm-6">
                                <label for="html5-date-input" class="col-md-2 col-form-label">End Date <span class="required">*</span></label>
                                <input class="form-control" type="date" id="html5-date-input" name="end_date" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Age <span class="required">*</span></label>
                                <input type="number" class="form-control" id="basic-default-image" name="age" placeholder="Age" />
                            </div>

                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" data-on="Active" data-off="Inactive" checked />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')