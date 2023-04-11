@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Event Organizer</h5>
            <a href="{{ route('EventOrganizer') }}" class="btn rounded-pill btn-primary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="card-body">
                    @foreach($eventOrganizerData as $eventOrganizerData1)
                    <div class="row">
                        <div class="row mb-3 col-sm-6">
                            <label class="col-label-form"><b>Name</b></label>
                            <div>
                                {{$eventOrganizerData1->name}}
                            </div>
                        </div>
                        <div class="row mb-3 col-sm-6">
                            <label class=" col-label-form"><b>Email</b></label>
                            <div>
                                {{ $eventOrganizerData1->email }}
                            </div>
                        </div>
                        <div class="row mb-3 col-sm-6">
                            <label class=" col-label-form"><b>Contact</b></label>
                            <div>
                                {{ $eventOrganizerData1->contact }}
                            </div>
                        </div>

                        <div class="row mb-3 col-sm-6">
                            <label class=" col-label-form"><b>Address</b></label>
                            <div>
                                {{ $eventOrganizerData1->address }}
                            </div>
                        </div>
                        <div class="row mb-3 col-sm-6">
                            <label class=" col-label-form"><b>State</b></label>
                            <div>
                                {!! $eventOrganizerData1->state !!}
                            </div>
                        </div>

                        <div class="row mb-3 col-sm-6">
                            <label class=" col-label-form"><b>Terms and Conditions</b></label>
                            <div>
                                {!! $eventOrganizerData1->terms_conditions!!}
                            </div>
                        </div>
                        <div class="row mb-3 col-sm-6">
                            <label class=" col-label-form"><b>Image</b></label>
                            <img src="{{ asset('Assets/images/' . $eventOrganizerData1->image) }}" class="img-thumbnail" />
                        </div>
                        <div class="col-sm-6">
                            <label><b>Status</b></label>
                            <div>
                                @if($eventOrganizerData1->status ==1)
                                <span class="badge bg-label-primary me-1">Active</span>
                                @else
                                <span class="badge bg-label-warning me-1">InActive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
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