@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-card-details"></i>
                </span> Edit CMS
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('portals.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" action="{{ route('portals.update',$portal->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="form-group col-md-6">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" value="{{ $portal->name }}" />
            </div>
            <div class="form-group col-md-6">
                <label for="image">Image</label>
                <input type="file" class="form-control" name="image" />
                <input type="hidden" name="hidden_Image" value="{{ $portal->image }}" />
            </div>
            <div class="form-group col-md-12">
                <label for="content">Content</label>
                <textarea name="content_editor" id="content" class="form-control" placeholder="Content">{{ $portal->content_editor }}</textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" placeholder="Status" {{ $portal->status==1 ? 'checked':'' }}>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
    </form>
</div>
@endsection('main_content')