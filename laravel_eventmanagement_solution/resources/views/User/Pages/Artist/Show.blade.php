@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<!-- bradcam_area -->
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="category-browse--header">
    <div class="category-browse--header">
        <div class="container">
            <div class="category-browse--header-text">
                <div class="category-browse--header-text__wrapper">
                    @foreach($artist as $art)
                    <h1 class="category-browse__header--content">Artists/{{ $art->name }}
                        <div class="eds-text-bl"></div>
                    </h1>
                    @endforeach
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section_title text-center mb-80">
                    <h3 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s"></h3>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($artist as $artistDetails)
            <div class="col-md-5">
                <div class="about_thumb">
                    <div class="shap_3  wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".4s">
                    </div>
                    <div class="thumb_inner  wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">
                        <img src="{{asset('Assets/images/' .$artistDetails->image)}}" alt="" width="400px" height="200px" class="astist_image">
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="about_info pl-68">
                    <h4 class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".5s">{{ ucfirst($artistDetails->name) }}</h4>
                    <h6><strong>Nick Name:</strong> {{$artistDetails->nick_name}}</h6>
                    <h6><strong>Email:</strong> {{$artistDetails->email}}</h6>
                    <h6><strong>Contact:</strong> {{$artistDetails->contact_number}}</h6>
                    <h6><strong>Country:</strong> {{$artistDetails->country}}</h6>
                </div>
            </div>
            @endforeach

            @if(count($expertize)>0)
            <div class="col-md-3 about_info pl-68">
                <h4 class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".5s">Artist Expertize</h4>
                @foreach($expertize as $index=>$exoer)
                {{$index+1}}.{{$exoer->name}}
                @endforeach
            </div>
            @endif

            @foreach($artist as $art)
            <div class="col-md-6 mt-4 about_info pl-68">

                <h4 class="wow fadeInLeft " data-wow-duration="1s" data-wow-delay=".5s">Bio</h4>
                <div class="scroller-height border bio">
                    {!! $art->bio !!}
                </div>

            </div>
            @endforeach
            @if($artist[0]->gallary_images)
            <div class="col-md-6 mt-4 about_info pl-68">
                <h4 class="wow">Gallery</h4>
                <div class="row gallery-item">
                @if(is_array(json_decode($gallary_images->gallary_images)))
                                <h2>Food Store Gallery</h2>
                                <?php
                                foreach (json_decode($gallary_images->gallary_images) as $index=>$a1) {
                                ?>
                                    <img src="{{asset('Assets/images/' .$a1)}}" id="image-<?php echo $index;?>" onclick="showLargeImage('<?php echo 'Assets/images/'.$a1 ?>', 'image-<?php echo $index;?>')" alt="" height="70px" width="70px">
                                <?php } ?>

                                @endif
                </div>
            </div>
            @endif
        </div>

        <!-- <div class="section mt-5 about_info pl-68">
            <h4 class="wow ">Gallery</h4>
            <div class="row gallery-item">
                @foreach($artist as $artistDetails1)
                @if(is_array(json_decode($artistDetails1->gallary_images)))
                <?php $a = json_decode($artistDetails1->gallary_images); ?>
                <div class="col-sm-12">
                    <div class="">
                        <?php for ($i = 0; $i < count($a); $i++) { ?>
                            <a class="img-pop-up ms-4">
                                <img class="single-gallery-image" src="{{asset('Assets/images/'.$a[$i]) }}" style="height:150px; width:150px" />
                            </a>
                        <?php } ?>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div> -->

    </div>
</div>
<script>
    function showLargeImage(imageSrc, imageId) {
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
                justifyContent: 'center',
                zIndex: 99,
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
<style>
    .astist_image {
        border-radius: 10px;
        /* position: fixed; */
    }
</style>
<!-- about_area_end  -->
@endsection('UserContent')