@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Ticket Category</h5>
                    <a href="{{ route('TicketCategory') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateTicketCategory',$ticketCategoryEdit->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-categoryname">Category Name</label>
                                <input type="text" class="form-control" id="basic-default-categoryname" name="name" placeholder="Category Name" value="{{ $ticketCategoryEdit->name }}" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Image</label>
                                <input type="file" class="form-control" id="image" name="image" />
                                <img src="{{ asset('Assets/images/' . $ticketCategoryEdit->image) }}" width="100" class="img-thumbnail" />
                                <input type="hidden" name="hidden_Image" value="{{ $ticketCategoryEdit->image }}" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-message">Description</label>
                                <textarea class="form-control content" name="description" placeholder="Terms & Conditions">{!! $ticketCategoryEdit->description !!}</textarea>

                                <!-- <textarea id="content" class="form-control" name="description" placeholder="Description about the Ticket..." >{!! $ticketCategoryEdit->description !!}</textarea> -->
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $ticketCategoryEdit->status=='1' ? 'checked':'' }} />
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