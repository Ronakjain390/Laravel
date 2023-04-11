@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Banner</h5>
                    <a href="{{ route('Banners') }}" class="btn rounded-pill btn-primary">Back</a>
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
                    <form action="{{ route('StoreBanner') }}" id="regForm" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="title">Banner Title <span class="required">*</span></label>
                            <input type="text" class="form-control" name="title" id="basic-default-fullname" placeholder="Banner Title" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">Description</label>
                            <textarea class="form-control content" name="description" placeholder="Banner Description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="image">Banner Image <span class="required">*</span></label>
                            <div>
                                <input type="file" id="image" name="image" class="form-control" required="" aria-describedby="image" multiple />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" checked />
                                <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    $(document).ready(function() {
        $("#regForm").validate({
            rules: {
                title: "required",
                description: "required",
                image: "required",
            }
        });

    });
</script>
@endpush
@endsection('SuperAdminContent')