@extends('admin/layouts/layout/base')
@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-comment-question-outline"></i>
                </span> Create FAQ
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
    <form method="POST" enctype="multipart/form-data" action="{{ route('faqs.store')}}" name="formPrevent">
        @csrf
        <div class="row mt-2">
            <div class=" form-group">

                <label for="question">Question <span class="required">*</span></label>
                <input type="text" class="form-control @error('question') is-invalid @enderror" name="question" id="question" required="" />
                @error('question')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
        </div>
        <div class="row mt-2">
            <div class="form-group">

                <label for="answer">Answer <span class="required">*</span></label>
                <textarea name="answer" id="answer" class="form-control @error('answer') is-invalid @enderror" required=""> </textarea>
                @error('answer')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="status">Status</label><br>
            <input type="checkbox" id="status" name="status" data-on="on" data-off="off" checked placeholder="Status">
        </div>
        <div class="form-group">
            <button type="submit" class="btn-submit btn btn-success btn-lg float-right px-5">Submit</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
<!-- jQuery UI 1.11.4 -->

<script>
    $(function() {
        $("form[name='formPrevent']").validate({
            rules: {
                question: {
                    required: true,
                    minlength: 4
                },
                answer: {
                    required: true,
                    minlength: 4
                },
            },
            messages: {
                question: {
                    required: "This field is required",
                    minlength: "Length must be more then Four"
                },
                answer: {
                    required: "This field is required",
                    minlength: "Length must be more then Four"
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
<script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-NSLHKCP');
</script>
<style>
    .error {
        color: red;
    }
</style>
@endsection('main_content')