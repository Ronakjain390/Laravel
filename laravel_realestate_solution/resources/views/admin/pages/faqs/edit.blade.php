@extends('admin/layouts/layout/base')
@section('main_content')
<div class="content-wrapper">
<div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-comment-question-outline"></i>
                </span> Edit Faq
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('faqs.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="POST" action="{{ route('faqs.update',$faq->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="form-group">
                <label for="title">Question</label>
                <input type="text" class="form-control" name="question" value="{{ $faq->question }} " />
            </div>
        </div>
        <div class="row mt-2">
            <div class="form-group">
                <label for="answer">Answer</label>
                <textarea id="content" class="form-control" name="answer">{{ $faq->answer }}</textarea>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="status">Status</label><br>
            <input type="checkbox" id="status" name="status" placeholder="Status" {{ $faq->status=='Active' ? 'checked':'' }}>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
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