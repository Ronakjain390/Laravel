@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-currency-usd"></i>
                </span> Create
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('currencies.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form action="{{ route('currencies.store') }}" method="POST" enctype="multipart/form-data" name="formPrevent">
        @csrf
        <div class="row mt-2">
            <div class="form-group col-md-4">
                <label for="country_name">Country Name <span class="required">*</span></label>
                <input type="text" class="form-control @error('country_name') is-invalid @enderror" id="country_name" name="country_name" required="" />
                @error('country_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="country_code">Country Code <span class="required">*</span></label>
                <input type="text" class="form-control @error('country_code') is-invalid @enderror" id="country_code" name="country_code" required="" />
                @error('country_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="currency_symbol">Currency Symbol <span class="required">*</span></label>
                <input type="text" class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol" name="currency_symbol" required="" />
                @error('currency_symbol')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="value">Value</label>
                <input type="text" class="form-control" id="value" name="value" />
            </div>
            <div class="form-group col-md-4">
                <label for="default">Set Currency as Default</label><br />
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="default" name="default">
                    <label class="custom-control-label" for="default"></label>
                </div>
            </div>
            <div class="form-group col-md-3">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" class="toggle-class" checked>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
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
                country_name: {
                    required: true,
                    minlength: 2,
                },
                country_code: {
                    required: true,
                },
                currency_symbol: {
                    required: true,
                },
            },
            messages: {
                country_name: {
                    required: "This field is required",
                    minlength: "Minimum two lateers required",
                },
                country_code: {
                    required: "This field is required",
                },
                currency_symbol: {
                    required: "This field is required",
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