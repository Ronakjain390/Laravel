@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<!-- bradcam_area -->
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="category-browse--header">
    <div class="category-browse--header-">
        <div class="container">
            <div class="category-browse--header-text">
                <div class="category-browse--header-text__wrapper">
                    <h2 class="category-browse__header--content" style="color:#FC86BC">Food Store/{{$foodStore->name}}
                        <div class="eds-text-bl"></div>
                    </h2>
                    <p></p>
                </div>
            </div>
            <!-- <aside class="category-browse--header-image category-browse--header-image--square"><img fetchpriority="high" class="full-width-img" loading="eager" src="" alt="[object Object]"></aside> -->
        </div>
    </div>
</div>

<!-- bradcam_area end -->
<!-- about_area_start  -->
<div class="about_area  extra_padd">

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="container-fluid">
                        <h1>{{$foodStore->name}}</h1>
                        <div class="row">
                            <div class="col-sm-3">
                                <img src="{{ asset('Assets/images/' . $foodStore->image) }}" alt="Food Store" height="200px" width="240px">
                            </div>
                            <div class="col-sm-7">
                                <h3>About Us</h3>
                                <p>{!! $foodStore->description !!}</p> <br>

                                <h3>Contact Us</h3>
                                <p>Address : {{$foodStore->address}}</p>
                                <p>Phone: {{$foodStore->contact_number}}</p>
                                <p>Email: {{$foodStore->email}}</p>
                            </div>
                            <div class="col-sm-6 mt-4">
                                @if(is_array(json_decode($foodStore->gallary_images)))
                                <h2>Food Store Gallery</h2>
                                <?php
                                foreach (json_decode($foodStore->gallary_images) as $index=>$a1) {
                                ?>
                                    <img src="{{asset('Assets/images/' .$a1)}}" id="image-<?php echo $index;?>" onclick="showLargeImage('<?php echo 'Assets/images/'.$a1 ?>', 'image-<?php echo $index;?>')" alt="" height="70px" width="70px">
                                <?php } ?>

                                @endif
                            </div>
                        </div>

                        <br><br><br><br>
                        <?php
                        $currency = currency();
                        $currency_symbol = $currency[0]->currency_symbol;
                        ?>
                        @if(count($foodmenu)>0)
                        <h3>Available Food Menu</h3>
                        <div class="row">
                            @foreach($foodmenu as $foodItem)
                            <div class="col-sm-3">
                                <div class="menu-item">
                                    <img class="img-fluid" src="{{ asset('Assets/images/' . $foodItem->image) }}" width="100" alt="Image">
                                    <h4>Name : {{$foodItem->name}}</h4>
                                    <p class="description"> Description :
                                         @if(Str::length($foodItem->description) > 40) 
                                         {!!str::substr($foodItem->description , 0 , 40 )!!} . '...' . 
                                         <a href="#" onclick='togglemodel(<?php echo $foodItem->id  ?>)'>View More</a>
                                         @else
                                         {!!$foodItem->description!!}
                                         @endif
                                    </p>
                                    <p class="text-muted">Price: {{ $currency[0]->currency_symbol }}{{$foodItem->price}}</p>
                                </div>
                            </div>
                            <!-- model value -->
                            <div id="myModel<?php echo $foodItem->id; ?>" class="modal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Food Menu Detail</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <p>{!! $foodItem->description !!}</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id="deleteButton" onclick="closeModel()" class="btn btn-secondary">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- model value -->
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let nikh


    // function togglemodel(id) {
    // nikh = id;
    // // alert(nikh)
    // document.getElementById('myModel').style.display = "block";
    // }

    function togglemodel(id) {
        nikh = id
        let mod = document.getElementById('myModel' + nikh).style.display = "block"
    }

    function closeModel() {
        let clos = document.getElementById('myModel' + nikh).style.display = "none"
    }
</script>
<style>
    p {
        color: black !important;
    }

    .card .card-body {
        height: auto;
        width: auto;
    }

    .card .card-body img {
        height: 200px;
        width: 100px;
    }

    .menu-item {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #fff;
        border-radius: 5px;
    }

    .menu-item:hover {
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    }

    .menu-item h4 {
        margin-top: 5px;
    }

    .menu-item p {
        margin-bottom: 0;
    }

    .col-sm-12 {
        margin-bottom: 20px;
    }

    .img-fluid {
        height: 150px;
    }

    .about_area {
        padding-bottom: 0px;
        /* position: relative; */
        margin-top: -2px;
    }

    p {
        margin-bottom: 0;
    }
</style>
<script>
    function showLargeImage(imageSrc, imageId ) {
        // console.log(imageId, imageSrc);
        // create a new image element with the large image source
        var largeImage = new Image();
        // console.log(largeImage);
        largeImage.src = `{{asset('Assets/images/' .$a1)}}`;
     

        // when the image is loaded, show it in an overlay
        largeImage.onload = function() {
            // create the overlay element
            var overlay = $('<div>').css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center'
            });

            // create the large image element and add it to the overlay
            var img = $('<img>').css({
                maxWidth: '50%',
                maxHeight: '50%',
            }).attr('src', `{{asset('${imageSrc}')}}`).appendTo(overlay);

            // add the overlay to the document
            $('body').append(overlay);

            // when the overlay is clicked, remove it from the document
            overlay.click(function() {
                overlay.remove();
            });
        };
    }
</script>
@endsection('UserContent')