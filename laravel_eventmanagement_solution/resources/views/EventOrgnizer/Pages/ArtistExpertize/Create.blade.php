@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create Artist Expertise</h4>
                    <a href="{{ route('OrganizerArtistExpertize') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <hr>
                <div class="card-body mt-3">
                    <form action="{{ route('StoreOrganizerArtistExpertize') }}" method="POST" name="formPrevent" id="regForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="expertize">Expertize <span class="required">*</span></label>
                                <input type="text" class="form-control" name="expertize" id="expertize" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="image">Image </label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="image" name="image" class="form-control" aria-describedby="image" multiple />
                                    <!-- <span class="input-group-text" id="image">Image</span> -->
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-12">
                                <label class="form-label" for="description">Description <span class="required">*</span></label>
                                <input type="text" class="form-control content" name="description" id="description" placeholder="Description" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" checked />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="btn" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#regForm").validate({
            rules: {
                expertize: "required",
                description: "required",
            }
        });
    });
</script>
@endsection('content')