@extends('user/layouts/parts/master')
@section('content')
<div class="">
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
@if(session('success'))
<div class="alert alert-success mb-1 mt-1">
    {{ session('success') }}
</div>
@endif
<!-- banner -->

<div class="container">
    <div class="spacer">
        <h3 class="page-title">Contact Us!</h3>
        <div class="row contact">
            <div class="col-lg-6 col-sm-6 ">
                <form action="{{ route('contactus.store') }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                    @csrf
                    <div class="row mt-2">
                        <div class="col-md-6 form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" name="name" id="name" required="" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" name="email" id="email" required="" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="contact-details">Contact Number:</label>
                            <input type="text" class="form-control" name="contact_details" id="contact_details" required="" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control" name="subject" id="subject" required="" />
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" required=""></textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-success" name="Submit">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 col-sm-6 ">
                <div class="well"><iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3559.33514628831!2d75.75578587543761!3d26.861091076677955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396db507851ce989%3A0x1fbb7541840e10aa!2sHeera%20Path%2C%20Ward%2027%2C%20Narayan%20Pura%2C%20Mansarovar%20Sector%206%2C%20Mansarovar%2C%20Jaipur%2C%20Rajasthan%20302020!5e0!3m2!1sen!2sin!4v1670501066012!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
            </div>
        </div>
    </div>
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
                contact_details: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                subject: {
                    required: true,
                },
                description: {
                    required: true,
                }

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