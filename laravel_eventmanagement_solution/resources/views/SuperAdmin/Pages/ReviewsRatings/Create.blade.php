@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Reviews and Rating</h5>
                    <a href="{{ route('Faqs') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('StoreReviewsRatings') }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="name">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="reviews">Reviews <span class="required">*</span></label>
                            <input type="text" class="form-control" name="reviews" id="reviews" placeholder="Reviews" />
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Rating</span>
                            </label>
                            <select id="rating" name="rating" class="form-control">
                                <option value="">-- Rating --</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Approve/Reject <span class="required">*</span>
                            </label>
                            <select id="approve_or_reject" name="is_approve" class="form-control">
                                <option value="">-- Approve / Reject --</option>
                                <option value="Approve">Approve</option>
                                <option value="Reject">Reject</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="image">Image <span class="required">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="file" id="image" name="image" class="form-control" required="" aria-describedby="image" multiple />
                                <span class="input-group-text" id="image">Image</span>
                            </div>

                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <input type="checkbox" id="status" name="status" data-on="Active" data-off="Inactive" checked placeholder="Status" />
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')