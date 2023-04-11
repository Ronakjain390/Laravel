@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Food Store</h4>
                    <a href="{{ route('FoodStoresOrganizer') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                @if (Session::has('success'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('UpdateFoodStoreOrganizer', $data->id) }}" method="POST" name="formPrevent" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="name">Store Name </label>
                                <input type="text" class="form-control" name="name" value="{{$data->name}}" id="name" placeholder="Name" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="description">Description </label>
                                <input type="text" class="form-control" name="description" value="{{$data->description}}" id="description" placeholder="Description" />
                            </div>

                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-email">Email </label>
                                <div class="input-group input-group-merge">
                                    <input type="text" id="basic-default-email" class="form-control" value="{{$data->email}}" placeholder="Email" name="email" aria-label="john.doe" aria-describedby="basic-default-email2" />
                                </div>
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-phone">Contact Number </label>
                                <input type="text" id="basic-default-phone" class="form-control phone-mask" value="{{$data->contact_number}}" name="contact_number" placeholder="Contact Number" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="address">Address </label>
                                <input type="text" class="form-control" value="{{$data->address}}" name="address" id="address" placeholder="Address" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="country">Country </label>
                                <input type="text" class="form-control" name="country" value="{{$data->country}}" id="country" placeholder="Country" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="state">State </label>
                                <input type="text" class="form-control" name="state" id="state" value="{{$data->state}}" placeholder="State" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="city">City </label>
                                <input type="text" class="form-control" name="city" id="city" value="{{$data->city}}" placeholder="City" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="postalcode">Postal code </label>
                                <input type="text" class="form-control" name="postal_code" value="{{$data->postal_code}}" id="postal_code" placeholder="Postal code" />
                            </div>

                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="image">Profile Image </label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="image" name="image" class="form-control" aria-describedby="image" />
                                    <span class="input-group-text" id="image">Image</span>
                                </div>
                                <img src="{{asset('Assets/images/' .$data->image)}}" alt="" width="150" height="100">
                                <input type="hidden" name="hidden_Image" value="{{ $data->image }}" />
                            </div>

                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="storevideo">Store Videos </label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="video" name="video" class="form-control" aria-describedby="video" />
                                    <span class="input-group-text" id="video">Videos</span>
                                </div>
                                <embed src="{{ asset('Assets/images/' . $data->video) }}" style="height:100px; width:150px" />

                            </div>

                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="image">Portfolio Images(Multiple) <span class="required">(Upload Multiple)</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="gallary_image" name="multiple_image[]" class="form-control" aria-describedby="image" multiple />
                                    <span class="input-group-text" id="gallary_image">Image</span>
                                </div>
                                @if(is_array(json_decode($data->gallary_images)))
                                <?php $a = json_decode($data->gallary_images); ?>
                                <?php for ($i = 0; $i < count($a); $i++) { ?>
                                    <a class="img-pop-up ms-4">
                                        <img class="single-gallery-image" src="{{asset('Assets/images/'.$a[$i]) }}" style="height:150px; width:150px" />
                                    </a>
                                <?php } ?>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $data->status=='1' ? 'checked':'' }} />
                                <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                            </div>
                        </div>
                        <input type="text" class="form-control" value="{{$data->longitude}}" name="longitude" id="longitude" hidden />
                        <input type="text" class="form-control" value="{{$data->latitude}}" name="latitude" id="latitude" hidden />

                        <!-- <h1>Add Food Items</h1> -->
                        <div class="row custom_heading">
                            <div class="col-sm-4">
                                <h3><b>Food Items</b></h3>
                            </div>
                            <div class="col-sm-8 addbutton_food">
                                <button type="button" name="add" id="add" class="btn btn-success">Add More</button>
                            </div>
                        </div>
                        @foreach($food_menu as $menu)
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="food_name">Food Name </label>
                                <input type="text" class="form-control" name="food_name[]" value="{{$menu->name}}" id="food_name" placeholder="State" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="price">Price </label>
                                <input type="text" class="form-control" name="food_price[]" value="{{$menu->price}}" id="food_price" placeholder="City" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Image</label>
                                <input type="file" class="form-control" id="food_images" name="food_images[]" />
                                <img src="{{ asset('Assets/images/' . $menu->image) }}" width="150" height="100" class="img-thumbnail" />
                                <input type="hidden" name="hidden_Image[]" value="{{ $menu->image }}" />

                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="basic-default-image">Video</label>
                                <input type="file" class="form-control" id="food_video" name="food_video[]" />
                                <embed src="{{ asset('Assets/images/' . $data->video) }}" style="height:100px; width:150px" />
                                <input type="hidden" name="hidden_Video[]" value="{{ $menu->video }}" />

                            </div>
                            <div class="mb-3 col-sm-12">
                                <label class="form-label" for="food_description">Food Description </label>
                                <input type="text" class="form-control content" name="food_description[]" value="{{$menu->description}}" id="food_description" placeholder="Description" />
                            </div>
                            <div class="mb-3 col-sm-10">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $menu->status=='1' ? 'checked':'' }} />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>

                            <div class="mb-3 col-sm-2">
                                <a href="{{  route('DeleteFoodMenu' ,$menu->id) }}" class="btn btn-danger"><i class="bx bx-trash"></i></a>
                            </div>
                        </div>
                        <br><br>
                        @endforeach

                        <div id="tableRow" class="Food_menu">
                            <div class="row food_content">
                                <div class="mb-3 col-sm-6">
                                    <label class="form-label" for="food_name">Food Name </label>
                                    <input type="text" class="form-control" name="food_name[]" id="food_name" placeholder="State" />
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <label class="form-label" for="price">Price </label>
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
                                <label class="form-label" for="food_description">Food Description </label>
                                <div class="mb-3 col-sm-12">
                                    <!-- <textarea name="food_description[]" id="food_description" cols="60" rows="3"></textarea> -->
                                    <input type="text" class="form-control content" name="food_description[]" id="food_description" placeholder="Description" />
                                </div>
                                <div class="mb-3 col-sm-3">
                                    <label class="form-label" for="basic-default-postal_code">Status</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="food_status[]" checked />
                                        <button type="button" class="btn btn-danger remove-tr">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // console.log('hello');
        var Data_to_clone = $('#tableRow').html();
        // console.log(Data_to_clone, "hello")
        var clicks = 0;
        $("#add").click(function() {
            // console.log('hei')
            clicks += 1;
            if (clicks == 1) {
                $('.remove-tr').css('display', 'block');
                $('.Food_menu').css('display', 'block');
            } else {
                $("#tableRow").append(Data_to_clone);
                $('.remove-tr').css('display', 'block');
            }
        });
    })


    $(document).on('click', '.remove-tr', function() {
        $(this).parents('.food_content').remove();
    });
</script>
<style>
    .Food_menu {
        display: none;
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
@endsection('content')