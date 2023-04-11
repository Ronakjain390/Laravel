@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Events Category</h5>
            <a href="{{ route('EventCategory') }}" class="btn rounded-pill btn-primary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <label><b>Category Name</b></label>
                    <div>
                        {{ $eventCategory->category_name }}
                    </div>
                </div>

                <div class="col-sm-6">
                    <label><b>Status</b></label>
                    <div>
                        @if($eventCategory->status ==1)
                        <span class="badge bg-label-primary me-1">Active</span>
                        @else
                        <span class="badge bg-label-warning me-1">InActive</span>
                        @endif
                    </div>
                </div>

                <div class="col-sm-12">
                    <b> Description </b>
                    <div>{!! $eventCategory->description !!}</div>
                </div>
                <div class="col-sm-6">
                    <b> Image </b>
                    <div class="img-fluid">
                        @if($eventCategory->image == '')
                        <img src="{{ asset('Assets/SuperAdmin/img/eventCategory/defaultProperty.jpeg') }}" class="img-thumbnail" alt="No Image" />
                        @else
                        <img src="{{ asset('Assets/images/' . $eventCategory->image) }}" class="img-thumbnail" alt="no Image" />
                        @endif
                    </div>
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