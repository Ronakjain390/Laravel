@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Artist's Expertize</h5>
                    <a href="{{ route('ArtistExpertize') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateArtistExpertize',$data->id) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="expertize">Expertize</label>
                                <input type="text" class="form-control" name="expertize" id="expertize" placeholder="Expertize" value="{{ $data->name }}" />
                            </div>

                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="image">Image</label>
                                <input type="file" class="form-control" id="image" name="image" />
                                <img src="{{ asset('Assets/images/' . $data->image) }}" width="100" class="img-thumbnail" />
                                <input type="hidden" name="hidden_Image" value="{{ $data->image }}" />
                            </div>
                            <div class="mb-3 col-sm-12">
                                <label class="form-label" for="answer">Description </label>
                                <input type="text" class="form-control content" name="description" id="description" placeholder="Description" value="{{ $data->description }}" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $data->status=='1' ? 'checked':'' }} />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
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