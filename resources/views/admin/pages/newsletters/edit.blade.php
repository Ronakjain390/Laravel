@extends('admin/layouts/layout/base')
@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-information"></i>
                </span> Edit Newsletters
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('newsletters.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="POST" action="{{ route('newsletters.update',$newsletter->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-4 form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" value="{{ $newsletter->name }} " />
            </div>

            <div class="col-md-4 form-group">
                <label for="subject">Subject</label>
                <input type="text" class="form-control" name="subject" value="{{ $newsletter->subject }} " />
            </div>
            <div class="form-group col-md-4">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" placeholder="Status" {{ $newsletter->status=='Active' ? 'checked':'' }}>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content">{{ $newsletter->content }}</textarea>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
        </div>

    </form>
</div>
@endsection('main_content')
<script>
    ClassicEditor
        .create(document.querySelector('#answer'))
        .catch(error => {
            console.error(error);
        });
</script>