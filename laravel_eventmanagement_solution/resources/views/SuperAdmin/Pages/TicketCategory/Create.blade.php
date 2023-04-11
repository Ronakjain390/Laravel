@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Event Ticket Category</h5>
                    <a href="{{ route('TicketCategory') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
              
                <div class="card-body">
                    <form action="{{ route('StoreTicketCategory')}}" id="regForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-categoryname">Category <span class="required">*</span></label>
                                <input type="text" class="form-control" id="basic-default-categoryname" name="name" placeholder="Category Name" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Image</label>
                                <input type="file" class="form-control" id="basic-default-image" name="image" placeholder="Image" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">Description<span class="required">*</span></label>
                            <textarea class="form-control content" name="description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" data-on="Active" data-off="Inactive" checked />
                                <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Send</button>
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
                editor: {
                    required: true
                }
            },
            messages: {
                editor: {
                    required: "Please enter some content"
                }

            },

        });

    });
</script>
@if(Session::has('success'))
<script>
    toastr.success("{{ Session::get('success') }}")
</script>
@endif
@endpush
@endsection('SuperAdminContent')