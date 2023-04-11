@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Event Category</h5>
                    <a href="{{ route('EventCategory') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateEventCategory',$eventCategoryEdit->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-categoryname">Category Name</label>
                            <input type="text" class="form-control" id="basic-default-categoryname" name="category_name" placeholder="Category Name" value="{{ $eventCategoryEdit->category_name }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-image">Image</label>
                            <input type="file" class="form-control" id="image" name="image" />
                            <img src="{{ asset('Assets/images/' . $eventCategoryEdit->image) }}" width="100" class="img-thumbnail" />
                            <input type="hidden" name="hidden_Image" value="{{ $eventCategoryEdit->image }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">Description</label>
                            <textarea class="form-control content" name="description" placeholder="Terms & Conditions">{!! $eventCategoryEdit->description !!}</textarea>

                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $eventCategoryEdit->status=='1' ? 'checked':'' }} />
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