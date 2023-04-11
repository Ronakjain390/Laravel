@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-more"></i>
                </span> Edit Reviews and Ratings
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('reviewsratings.index') }}" class="btn btn-success btn-lg float-right">Back</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    @if(session('status'))
    <div class="alert alert-success mb-1 mt-1">
        {{ session('status') }}
    </div>
    @endif
    <form method="POST" action="{{ route('reviewsratings.update',$reviewsrating->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-4 form-group">
                <label for="name">Name </label>
                <input type="text" name="name" class="form-control" value="{{ $reviewsrating->name }}">
            </div>
            <div class="col-md-4 form-group">
                <label>Rating</span></label>
                <select id="rating" name="rating" class="form-control">
                    @if($reviewsrating->rating == '')
                    <option>Null</option>
                    @else
                    <option>{{ $reviewsrating->rating }}</option>
                    @endif
                    <div class="dropdown-divider"></div>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>Approve/Reject</label>
                <select id="approve_or_reject" name="is_approve" class="form-control">
                    <option>{{ $reviewsrating->is_approve }}</option>
                    <option value="Approve">Approve</option>
                    <option value="Reject">Reject</option>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="reviews">Review</label>
                <textarea name="reviews" id="reviews" class="form-control">{{ $reviewsrating->reviews }}</textarea>
            </div>
            <div class="col-md-5 form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control" name="image" value="c:kjs" />
                <img src="{{ asset('assets/images/reviewsratings/' . $reviewsrating->image) }}" width="100" class="img-thumbnail mt-1" />
                <input type="hidden" name="hidden_Image" value="{{ $reviewsrating->image }}" />
            </div>
            <div class="form-group col-md-2">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" {{ $reviewsrating->status=='1' ? 'checked':'' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')