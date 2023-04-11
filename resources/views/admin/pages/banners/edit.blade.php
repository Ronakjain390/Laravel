@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
<div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-flag-plus"></i>
                </span> Edit Banner
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('banners.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" action="{{ route('banners.update',$banner->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" value="{{ $banner->title }}" />
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" id="content" class="form-control">{{ $banner->description }}</textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="banner_image">Banner Image</label>
                <input type="file" class="form-control" id="banner_image" name="image" />
                <img src="{{ asset('assets/images/banner/' . $banner->image) }}" width="100" class="img-thumbnail" />
                <input type="hidden" name="hidden_Image" value="{{ $banner->image }}" />
                @error('Image')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" class="toggle-class" {{ $banner->status=='1' ? 'checked':'' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')