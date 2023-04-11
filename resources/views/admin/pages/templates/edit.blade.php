@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
<div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-checkbox-multiple-blank"></i>
                </span> Edit Template
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('templates.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="POST" action="{{ route('templates.update',$template->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-6 form-group">
                <label for="templatename">Template name</label>
                <input type="text" class="form-control" name="name" id="templatename" placeholder="Template name" value="{{ $template->name }}" />
            </div>
            <div class="col-md-6 form-group">
                <label for="templatesubject">Template Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="Template subject" id="templatesubject" value="{{ $template->subject }}">
            </div>
            <div class="col-md-12 form-group">
                <label for="templatecontent">Template Content</label>
                <textarea name="content" class="form-control" id="content">{{ $template->content }}</textarea>
            </div>
            <div class="form-group col-md-3">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" class="toggle-class" {{ $template->status=='1' ? 'checked':'' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')

<script>
    ClassicEditor
        .create(document.querySelector('#content'))
        .catch(error => {
            console.error(error);
        });
</script>
