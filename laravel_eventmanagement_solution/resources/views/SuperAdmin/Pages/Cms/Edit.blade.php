@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Cms</h5>
                    <a href="{{ route('Cms') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    @if(session('status'))
                    <div class="alert alert-success mb-1 mt-1">
                        {{ session('status') }}
                    </div>
                    @endif
                    <form action="{{ route('UpdateCms',$cmsEdit->id) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @method('PUT')
                        @csrf
                        <div class="mb-3">

                            <label for="title">Title <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $cmsEdit->name }}" placeholder="Name" id="name" required="">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">

                            <label for="description">Content <span class="required">*</span></label>
                            <textarea name="description" id="content" class="form-control content">{{ $cmsEdit->description }}</textarea>
                            @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-image">Image</label>
                            <input type="file" class="form-control" id="image" name="image" />
                            <img src="{{ asset('Assets/images/' . $cmsEdit->image) }}" width="100" class="img-thumbnail" />
                            <input type="hidden" name="hidden_Image" value="{{ $cmsEdit->image }}" />

                        </div>

                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $cmsEdit->status=='1' ? 'checked':'' }} />
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