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

    <form method="POST" action="{{ route('destinations.update',$destination->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" value="{{ $destination->title }}" />
            </div>
            <div class="col-md-12 form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control">{{ $destination->address }}</textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" id="content" class="form-control">{{ $destination->description }}</textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="image">Image</label>
                <input class="form-control" type="file" name="image[]" multiple />
                <?php foreach (json_decode($destination->image) as $picture) { ?>

                    <img src="{{ asset('assets/images/destinations/' . $picture) }}" width="100" class="img-thumbnail mt-1" />

                    <input type="hidden" name="hidden_Image" value="{{ $picture }}" />
                <?php } ?>
                @error('Image')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')