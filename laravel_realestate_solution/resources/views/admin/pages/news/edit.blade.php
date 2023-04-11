@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-newspaper"></i>
                </span> Edit News
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('news.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="POST" action="{{ route('news.update',$news->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-12  form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" value="{{ $news->title }} " />
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 form-group">
                <label for="descriptiom">Description</label>
                <textarea name="description" id="content" class="form-control">{{ $news->description }}</textarea>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6 form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control" name="image" value="c:kjs" />
                <img src="{{ asset('assets/images/news/' . $news->image) }}" width="100" class="img-thumbnail" />
                <input type="hidden" name="hidden_Image" value="{{ $news->image }}" />
                @error('Image')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" placeholder="Status" {{ $news->status=='1' ? 'checked':'' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>

    </form>
</div>

@endsection('main_content')