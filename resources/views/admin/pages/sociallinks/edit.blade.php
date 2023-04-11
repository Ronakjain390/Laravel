@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-feather"></i>
                </span> Edit
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('sociallink.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" action="{{ route('sociallink.update',$links->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="form-group col-md-4">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $links->name }}" />
            </div>
            <div class="form-group col-md-4">
                <label for="url">URL</label>
                <input type="text" class="form-control" id="url" name="url" value="{{ $links->url }}" />
            </div>
            <div class="col-md-4 form-group">
            </div>
            <div class="form-group col-md-4">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" class="toggle-class" {{ $links->status=='1' ? 'checked':'' }}>
            </div>
            <div class="col-md-4 form-group">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-info btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>

@endsection('main_content')