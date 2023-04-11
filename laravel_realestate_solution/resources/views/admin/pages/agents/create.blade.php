@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-box-multiple"></i>
                </span> Create
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('agents.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="POST" enctype="multipart/form-data" action="{{ route('agents.store') }} " autocomplete="off" name="formPrevent">
        @csrf
        <div class="row mt-2">
            <div class="col-md-3 form-group">
                <label for="name">Name <span class="required">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" required="" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" required="" />
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" name="password" id="password" required="" />
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="contact">Contact No. <span class="required">*</span></label>
                <input type="text" class="form-control @error('contact') is-invalid @enderror" name="contact" id="contact" required="" />
                @error('contact')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="address">Address <span class="required">*</span></label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" required="" />
                @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="city">City <span class="required">*</span></label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" id="city" required="" />
                @error('city')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="state">State <span class="required">*</span></label>
                <input type="text" class="form-control @error('state') is-invalid @enderror" name="state" id="state" required="" />
                @error('state')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="country">Country <span class="required">*</span></label>
                <input type="text" class="form-control @error('country') is-invalid @enderror" name="country" id="country" required="" />
                @error('country')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="postal_code">Postal Code <span class="required">*</span></label>
                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" id="postal_code" required="" />
                @error('postal_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="agent_profile_image">Image <span class="required">*</span></label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image" required="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-3">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" class="toggle-class" checked>
            </div>
            <div class="col-md-6 form-group">
                <input type="text" class="form-control" value="2" name="type" hidden />
                <input type="text" class="form-control" name="username" id="username" hidden />
                <input type="text" class="form-control" name="longitude" id="longitude" hidden />
                <input type="text" class="form-control" name="latitude" id="latitude" hidden />
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
                    minlength: 2,
                },
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true,
                    minlength: 8,
                },
                contact: {
                    required: true,
                    digits: true,
                    min: 10,
                    max: 10
                },
                address: {
                    required: true,
                },
                city: {
                    required: true,
                },
                state: {
                    required: true,
                },
                country: {
                    required: true,
                },
                postal_code: {
                    required: true,
                    digits: true
                },
                image: {
                    required: true,
                },
            },
            messages: {
                email: {
                    required: "Please enter a valid email address",
                    email: "email must contain @ and . ",
                },
                contact_detail: {
                    min: "length must be equal to 10",
                    max: "length must be equal to 10",
                    digits: "must contain only integer"
                }
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