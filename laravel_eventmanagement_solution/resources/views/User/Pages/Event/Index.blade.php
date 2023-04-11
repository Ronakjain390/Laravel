@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="category-browse--header">
    <div class="category-browse--header-">
        <div class="container" style="padding: 1rem;">
            <div class="category-browse--header-text">
                <div class="category-browse--header-text__wrapper">
                    <h1 class="category-browse__header--content">Events <div class="eds-text-bl" style="color:#FFF58C;padding-top:8px"></div>
                    </h1>
                    <p>Discover the best Performing &amp; Visual Arts events in your area and online</p>
                </div>
            </div>
        </div>
        <!-- <aside class="category-browse--header-image category-browse--header-image--square"><img fetchpriority="high" class="full-width-img" loading="eager" src="" alt="[object Object]"></aside> -->
    </div>
</div>
<section class="blog_area section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="blog_right_sidebar">
                    <aside class="single_sidebar_widget post_category_widget">
                        <h4 class="widget_title">Filters</h4>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Type
                                </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <input type="checkbox" data-id="0" class="type" id="type" name="type" value="0">
                                        <label for="vehicle1"> Offline</label><br>
                                        <input type="checkbox" data-id="1" class="type" id="type" name="type" value="1">
                                        <label for="vehicle1"> Online</label><br>
                                        <input type="checkbox" data-id="2" class="type" id="type" name="type" value="2">
                                        <label for="vehicle1"> Both (Online & Offline)</label><br>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Category
                                </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    @foreach($eventCategory as $eventCategory1)
                                    <input type="checkbox" data-id="{{ $eventCategory1->id }}" class="category" id="category" name="category" value="{{ $eventCategory1->id }}">
                                    <label> {{ $eventCategory1->category_name }}</label><br>
                                    @endforeach
                                </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    City
                                </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    @foreach($eventCity as $eventCity1)
                                    @if(!is_null($eventCity1->city))
                                    <input type="checkbox" data-id="{{ $eventCity1->city }}" class="city" id="city" name="city" value="{{ $eventCity1->city }}">
                                    <label> {{ $eventCity1->city }}</label><br>
                                    @endif
                                    @endforeach
                                </div>
                                </div>
                            </div>
                        </div>
                        <!-- <h5>Type</h5>
                        <input type="checkbox" data-id="0" class="type" id="type" name="type" value="0">
                        <label for="vehicle1"> Offline</label><br>
                        <input type="checkbox" data-id="1" class="type" id="type" name="type" value="1">
                        <label for="vehicle1"> Online</label><br>
                        <input type="checkbox" data-id="2" class="type" id="type" name="type" value="2">
                        <label for="vehicle1"> Both</label><br>

                        <h5>Category</h5>

                        @foreach($eventCategory as $eventCategory1)
                        <input type="checkbox" data-id="{{ $eventCategory1->id }}" class="category" id="category" name="category" value="{{ $eventCategory1->id }}">
                        <label> {{ $eventCategory1->category_name }}</label><br>
                        @endforeach
                        <h5>City</h5>
                        @foreach($eventCity as $eventCity1)
                        @if(!is_null($eventCity1->city))
                        <input type="checkbox" data-id="{{ $eventCity1->city }}" class="city" id="city" name="city" value="{{ $eventCity1->city }}">
                        <label> {{ $eventCity1->city }}</label><br>
                        @endif
                        @endforeach -->
                    </aside>

                    <!-- <aside class="single_sidebar_widget newsletter_widget">
                        <h4 class="widget_title">Newsletter</h4>

                        <form action="#">
                            <div class="form-group">
                                <input type="email" class="form-control" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter email'" placeholder='Enter email' required>
                            </div>
                            <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn" type="submit">Subscribe</button>
                        </form>
                    </aside> -->
                </div>
            </div>
            <div class="col-lg-9 event-type">
                @include('User.Pages.Event.EventFilter')
            </div>
        </div>
      
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.pagination a', function(event) {
            // alert(sortby);
            event.preventDefault();
            // var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];

            fetch_data(page);
        });
        $(document).on('click', '.type , .category, .city', function() {
            var type = [];
            var flag = 1;
            var category = [];
            var city = [];

            $("input:checkbox[name=type]:checked").each(function() {
                type.push($(this).data("id"));
            });
            $("input:checkbox[name=category]:checked").each(function() {
                category.push($(this).data("id"));
            });
            $("input:checkbox[name=city]:checked").each(function() {
                city.push($(this).data("id"));
            });
            // alert(category);
            $.ajax({
                url: '{{route("EventShow") }}',
                type: "GET",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "flag": flag,
                    "type": type,
                    "category": category,
                    "city": city,

                },
                success: function(data) {
                    console.log(data.html);
                    $('#event-type-list').html(data.html);
                },
                error: function(data) {
                    console.log("error");
                    $('#event-type-list').html(data.html);
                }
            });
        });
    });

    function fetch_data(page) {
        // alert(page);
        var flag = 1;
        $.ajax({
            url: "?page=" + page + "&sortby=" + sortby,
            type: "post",
            datatype: "html",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content')
            },
            data: {
                "flag": flag,
                "fromsource": fromsource,
                "checkin": checkin,
                "checkout": checkout,
                "days": days,
                "member": member,
                "rooms": rooms
            },
            success: function(data) {

                if (data.success == true) {
                    console.log("hello");
                    // console.log(data.html);
                    $('body').find('.hotel-list-data').html(data.html);
                }
            },
            error: function(jqXHR, ajaxOptions, thrownError) {
                console.log(7);
                alert('No response from server');
            }
        });
    }
</script>

@endsection('UserContent')