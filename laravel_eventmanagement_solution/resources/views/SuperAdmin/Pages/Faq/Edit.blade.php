@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit FAQ's</h5>
                    <a href="{{ route('Faqs') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateFaq',$data->id) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @method('PUT')
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="question">Question <span class="required">*</span></label>
                            <input type="text" class="form-control" name="question" id="question" placeholder="Question" value="{{ $data->question }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">Answer<span class="required">*</span></label>
                            <textarea class="form-control content" name="answer"> {{ $data->answer }}</textarea>
                        </div>

                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $data->status=='1' ? 'checked':'' }} />
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