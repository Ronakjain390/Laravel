@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-more"></i>
                </span> Create City
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('cities.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form action="{{ route('cities.store') }}" method="POST" enctype="multipart/form-data" name="formPrevent">
        @csrf
        <div class="row mt-2">
        <div class="col-md-3 form-group">
            <label for="countryname">Country <span class="required">*</span></label>
            <select class="form-control @error('countryname') is-invalid @enderror" name="countryid" id="countryid">
                <option selected>--Select Country--</option>
                @foreach($country as $country)
                <option value="{{$country->id}}">{{$country->countryname}}</option>
                @endforeach
            </select>
            @error('countryname')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="col-md-3 form-group">
            <label for="countryname">State <span class="required">*</span></label>
            <select class="form-control @error('state') is-invalid @enderror" name="stateid" id="stateid">
                <option selected>--Select State--</option>
                @foreach($states as $state)
                <option value="{{$state->id}}">{{$state->statename}}</option>
                @endforeach
            </select>
            @error('state')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
            <div class="col-md-4 form-group">
                <label for="countryname">City Name <span class="required">*</span></label>
                <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" placeholder="City Name"  />
                @error('city')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-2">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" data-on="on" data-off="off" checked>
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
                name: {
                    required: true,
                },
                reviews: {
                    required: true,
                },
                is_approve: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "This field is required",
                },
                reviews: {
                    required: "This field is required",
                },
                is_approve: {
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