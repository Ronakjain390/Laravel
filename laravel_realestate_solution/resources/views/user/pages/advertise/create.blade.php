@extends('user/layouts/parts/master')
@section('content')
<div class="">
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
<br>
<div class="container">
    <div class="content-wrapper width: 200px;">
        <div class="row">
            <div class="col-md-6">
                <b>
                    <h2>Add Advertise</h2>
                </b>
            </div>
        </div>
        @if(session('status'))
        <div class="alert alert-success mb-1 mt-1">
            {{ session('status') }}
        </div>
        @endif
        <div class="container">
            <form method="POST" enctype="multipart/form-data" action="{{ route('advertiseus.store') }}" name="formPrevent">
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" />
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="contactNumber">Contact Number <span class="required">*</span></label>
                        <input type="text" class="form-control @error('contactNumber') is-invalid @enderror" name="contactNumber" id="contactNumber" required="" />
                        @error('contactNumber')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="title">Title <span class="required">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" id="title" required="" />
                        @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="Utype">User Type <span class="required">*</span></label>
                        <select class="form-control @error('Utype') is-invalid @enderror" name="Utype" id="Utype">
                            <option selected>--Select User Type--</option>
                            <option value="owner">Owner</option>
                            <option value="agent">Agent</option>
                        </select>
                        @error('Utype')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="reason">Sell or Rent <span class="required">*</span></label>
                        <select class="form-control @error('Utype') is-invalid @enderror" name="reason" id="reason">
                            <option selected>--Select Sell or Rent--</option>
                            <option value="sell">Sell</option>
                            <option value="rent">Rent</option>
                        </select>
                        @error('reason')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="priceType">Price Type <span class="required">*</span></label>
                        <select class="form-control @error('Utype') is-invalid @enderror" name="priceType" id="priceType">
                            <option selected>--Select Price Type--</option>
                            <option value="per square feet">Per square feet</option>
                            <option value="total">Total</option>
                        </select>
                        @error('priceType')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="price">Price <span class="required">*</span></label>
                        <input type="text" class="form-control @error('totalPrice') is-invalid @enderror" name="totalPrice" id="price" required="" />
                        @error('totalPrice')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="builtArea">Builtup Area <span class="required">*</span></label>
                        <input type="text" class="form-control @error('builtArea') is-invalid @enderror" name="builtArea" id="builtArea" required="" />
                        @error('builtArea')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="carpetArea">Carpet Area <span class="required">*</span></label>
                        <input type="text" class="form-control @error('carpetArea') is-invalid @enderror" name="carpetArea" id="carpetArea" required="" />
                        @error('carpetArea')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="bedroom">Bedroom <span class="required">*</span></label>
                        <input type="text" class="form-control @error('bedroom') is-invalid @enderror" name="bedroom" id="bedroom" required="" />
                        @error('bedroom')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="bathroom">Bathroom <span class="required">*</span></label>
                        <input type="text" class="form-control @error('bathroom') is-invalid @enderror" name="bathroom" id="bathroom" required="" />
                        @error('bathroom')
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
                        <input type="text" class="form-control" name="postal_code" id="postal_code" required="" />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="image">Image's of Property <span class="required">*</span></label>
                        <input type="file" class="form-control" name="image" id="image" multiple required="" />
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="amenities">General Amenities</label><br>
                        <select class="form-control " name="amenities" id="amenities">
                            @foreach ($amenities as $amenities)
                            <option value="{{ $amenities->id }}">{{ $amenities->amenities }}</option>
                            @endforeach
                        </select>
                    </div>
                    

                    <div class="col-md-12 form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="content" class="form-control"></textarea>
                    </div>
                    <input type="hidden" class="form-control" name="longitude" id="longitude" hidden />
                    <input type="hidden" class="form-control" name="latitude" id="latitude" hidden />
                    <br>
                    <div class="col-md-12 form-group">
                        <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<br>

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
                contactNumber: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                title: {
                    required: true,
                },
                Utype: {
                    required: true,
                },
                reason: {
                    required: true,
                },
                priceType: {
                    required: true,
                },
                totalPrice: {
                    required: true,
                    digits: true
                },
                builtArea: {
                    required: true,
                    digits: true
                },
                carpetArea: {
                    required: true,
                    digits: true
                },
                bedroom: {
                    required: true,
                    digits: true
                },
                bathroom: {
                    required: true,
                    digits: true
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

@endsection('content')