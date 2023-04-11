@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create social Link</h5>
                    <a href="{{ route('SocialLinks') }}" class="btn rounded-pill btn-primary">Back</a>
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
                    <form action="{{ route('StoreSocialLinks') }}" method="POST" id="regForm" enctype="multipart/form-data" name="formPrevent">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="name">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="url">URL <span class="required">*</span></label>
                            <input type="text" class="form-control" name="url" id="url" placeholder="URL" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" data-on="Active" data-off="Inactive" checked />
                                <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                            </div>
                        </div> <br>
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
                name: "required",
                url: "required",
            }
        });

    });
</script>
@endpush
@endsection('SuperAdminContent')