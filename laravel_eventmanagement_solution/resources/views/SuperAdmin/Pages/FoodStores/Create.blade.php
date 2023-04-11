@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Food Store</h5>
                    <a href="{{ route('FoodStores') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                @if (Session::has('success'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('StoreFoodStore') }}" id="regForm" method="POST" name="formPrevent" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="name">Store Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-email">Email <span class="required">*</span></label>

                                <input type="text" id="basic-default-email" class="form-control" placeholder="Email" name="email" aria-label="john.doe" aria-describedby="basic-default-email2" />

                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">Contact Number <span class="required">*</span></label>
                                <input type="text" id="basic-default-phone" class="form-control phone-mask" name="contact_number" placeholder="Contact Number" />
                            </div>


                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="address">Address <span class="required">*</span></label>
                                <input type="text" class="form-control" name="address" id="address" placeholder="Address" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="country">Country <span class="required">*</span></label>
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="state">State <span class="required">*</span></label>
                                <input type="text" class="form-control" name="state" id="state" placeholder="State" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="city">City <span class="required">*</span></label>
                                <input type="text" class="form-control" name="city" id="city" placeholder="City" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="postalcode">Postal code <span class="required">*</span></label>
                                <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Postal code" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" checked />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Profile Image <span class="required">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="image" name="image" class="form-control" aria-describedby="image" multiple />
                                    <span class="input-group-text" id="image">Image</span>
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="storevideo">Store Videos </label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="video" name="video" class="form-control" aria-describedby="video" multiple />
                                    <span class="input-group-text" id="video">Video</span>
                                </div>
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Portfolio Images (Multiple)</label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="gallary_image" name="gallary_image[]" class="form-control" aria-describedby="image" multiple />
                                    <span class="input-group-text" id="gallary_image">Image</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 col-sm-12">
                            <label class="form-label" for="description">Description <span class="required">*</span></label>
                            <input type="text" class="form-control content" name="description" id="description" placeholder="Description" />
                        </div>

                        <input type="text" class="form-control" name="longitude" id="longitude" hidden />
                        <input type="text" class="form-control" name="latitude" id="latitude" hidden />

                        <div class="row custom_heading">
                            <div class="col-sm-4">
                                <h3><b>Add Food Items</b></h3>
                            </div>
                            <div class="col-sm-8 addbutton_food">
                                <button type="button" name="add" id="add" class="btn btn-success">Add More</button>
                            </div>
                        </div>
                        <div id="tableRow">
                            <div class="row food_content">
                                <div class="mb-3 col-sm-6">
                                    <label class="form-label" for="food_name">Food Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="food_name[]" id="food_name" placeholder="State" />
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <label class="form-label" for="price">Price <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="food_price[]" id="food_price" placeholder="Price" />
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <label class="form-label" for="image">Food Image<span class="required">*</span> </label>
                                    <div class="input-group input-group-merge">
                                        <input type="file" id="image" name="food_image[]" class="form-control" aria-describedby="image" multiple />
                                        <span class="input-group-text" id="image">Image</span>
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <label class="form-label" for="image">Food Video </label>
                                    <div class="input-group input-group-merge">
                                        <input type="file" id="food_video" name="food_video[]" class="form-control" aria-describedby="image" multiple />
                                        <span class="input-group-text" id="image">Video</span>
                                    </div>
                                </div>
                                <label class="form-label" for="food_description">Food Description <span class="required">*</span></label>
                                <div class="mb-3 col-sm-12">
                                    <!-- <textarea name="food_description[]" id="food_description" cols="60" rows="3"></textarea> -->
                                    <input type="text" class="form-control content" name="food_description[]" id="food_description" placeholder="Description" />
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <label class="form-label" for="basic-default-postal_code">Status</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="food_status[]" checked />
                                        <button type="button" class="btn btn-danger remove-tr">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script type="text/javascript">
    var Data_to_clone = $('#tableRow').html();
    console.log(Data_to_clone, "hello")
    $("#add").click(function() {
        $("#tableRow").append(Data_to_clone);
        $('.remove-tr').css('display', 'block');
    });

    $(document).on('click', '.remove-tr', function() {
        $(this).parents('.food_content').remove();
    });
</script>
<script>
    $(document).ready(function() {
        $("#regForm").validate({
            rules: {
                name: "required",
                description: "required",
                email: "required",
                contact_number: "required",
                address: "required",
                country: "required",
                state: "required",
                city: "required",
                postal_code: "required",
                'food_description[]': "required",
                'food_name[]': "required",
                'food_price[]': "required",
            }
        });

    });
</script>
@endpush

<style>
    .custom_heading {
        display: flex;
    }

    .addbutton_food {
        text-align: right;
    }

    .remove-tr {
        display: none;
        /* background-color: #b02a37; */
        margin-left: 50px;
    }
</style>
@endsection('SuperAdminContent')