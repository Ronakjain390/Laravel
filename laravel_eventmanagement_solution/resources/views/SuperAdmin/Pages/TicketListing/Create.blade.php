@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Basic Layout</h5>
                    <small class="text-muted float-end">Default label</small>
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
                    <form action="{{ route('StoreEventOrganizer') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-categoryname">Name</label>
                                <input type="text" class="form-control" id="basic-default-categoryname" name="name" placeholder="Name" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label for="exampleFormControlSelect1" class="form-label">Language</label>
                                <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example" name="language">
                                    <option selected name="event_type">Select Language</option>
                                    <option>Hindi</option>
                                    <option>English</option>
                                </select>
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label for="html5-date-input" class="col col-form-label">Time Duration</label>
                                <input class="form-control" type="text" name="time_duration" id="html5-date-input" placeholder="Enter Time Duration" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-categoryname">Age </label>
                                <input type="text" class="form-control" id="basic-default-categoryname" name="age" placeholder="Enter Age limit" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">About Event</label>
                            <textarea class="form-control content" name="about_event" placeholder="Description about the Event..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">Terms & Conditions</label>
                            <textarea class="form-control content" name="terms_conditions" placeholder="Terms & Conditions"></textarea>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Image</label>
                                <input type="file" class="form-control" id="basic-default-image" name="image" placeholder="Image" />
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