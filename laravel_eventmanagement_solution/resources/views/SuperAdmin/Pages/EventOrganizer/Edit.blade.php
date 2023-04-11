@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Event Organizer </h5>
                    <a href="{{ route('EventOrganizer') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateEventOrganizer' ,$eventOrganizerData->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="firstName" class="form-label">Name</label>
                                <input class="form-control" type="text" id="firstName" name="name" value="{{ $eventOrganizerData->name }}" autofocus />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control" type="text" id="email" name="email" value="{{ $eventOrganizerData->email }}" placeholder="john.doe@example.com" />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phoneNumber">Phone Number</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">(+91)</span>
                                    <input type="text" id="phoneNumber" name="contact_number" value="{{$eventOrganizerData->contact_number }}" class="form-control" placeholder="202 555 0111" />
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $eventOrganizerData->address }}" placeholder="Address" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">State</label>
                                <input class="form-control" type="text" id="state" name="state" value="{{$eventOrganizerData->state }}" placeholder="State" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">City</label>
                                <input class="form-control" type="text" id="city" name="city" value="{{$eventOrganizerData->city }}" placeholder="City" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="zipCode" class="form-label">Zip Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ $eventOrganizerData->postal_code }}" placeholder="231465" maxlength="6" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="country">Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="{{ $eventOrganizerData->country }}" placeholder="231465" maxlength="6" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Image</label>
                                <input type="file" class="form-control" id="image" name="image" />
                                <img src="{{ asset('Assets/images/' . $eventOrganizerData->image) }}" width="100" class="img-thumbnail" />
                                <input type="hidden" name="hidden_Image" value="{{ $eventOrganizerData->image }}" />

                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $eventOrganizerData->status=='1' ? 'checked':'' }} />
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