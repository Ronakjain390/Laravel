@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Template</h5>
                    <a href="{{ route('Templates') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateTemplate',$data->id) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @method('PUT')
                        @csrf
                        <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="name">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name"  value="{{ $data->name }}"/>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="subject">Subject <span class="required">*</span></label>
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" value="{{ $data->subject }}" />
                        </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">Content <span class="required">*</span></label>
                            <textarea class="form-control content" name="content" placeholder="Description "> {{$data->content}} </textarea>
                        </div>
                        <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $data->status=='1' ? 'checked':'' }}   />
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