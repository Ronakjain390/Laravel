@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')

<div class="pagetitle">
    <div class="header22">
        <h1>Event</h1>
        <a type="button" class="btn btn-primary" href="{{ route('listevents') }}">Back</a>
    </div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('listevents') }}">Event</a></li>
            <!-- <li class="breadcrumb-item">Events</li> -->
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <form action="{{ route('storeevent') }}" id="regForm" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="mb-3 col-sm-4">
                <label class="form-label" for="basic-default-title">Title <span class="required">*</span></label>
                <input type="text" class="form-control" id="basic-default-title" placeholder="Title" name="title" />
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
                <label for="html5-date-input" class="col-md-4 col-form-label">Start Date <span class="required">*</span></label>
                <input class="form-control datepicker " type="text" name="start_date" />
            </div>
            <div class="mb-3 col-sm-4">
                <label for="html5-date-input" class="col-md-4 col-form-label">End Date <span class="required">*</span></label>
                <input class="form-control datepicker" type="text" id="html5-date-input" name="end_date" />
            </div>
            <div class="mb-3 col-sm-4">
                <label class="form-label" for="basic-default-postal_code">Start Time<span class="required">*</span></label>
                <input type="time" id="start_time" class="form-control venue" name="start_time" placeholder="Enter Start Time " />
            </div>

            <div class="mb-3 col-sm-4">
                <label class="form-label" for="basic-default-postal_code">End Time<span class="required">*</span></label>
                <input type="time" id="end_time" class="form-control venue" name="end_time" placeholder="Enter End Time  " />
            </div>

            <div class="mb-3 col-sm-4">
                <label class="form-label" for="basic-default-postal_code">Duration<span class="required">*</span></label>
                <input type="text" id="duration_id" class="form-control venue" name="duration" placeholder="Duration of event " disabled />
            </div>
            <div class="mb-3 col-sm-4">
                <label for="exampleFormControlSelect1" class="form-label">Category <span class="required">*</span></label>
                <select class="form-select" name="category_id" id="exampleFormControlSelect1" aria-label="Default select example">
                    <option selected value=" ">Select Event Category</option>
                    @foreach($eventCategoryData as $data)
                    <option value="{{$data->id}}">{{ $data->category_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-sm-4">
                <label for="exampleFormControlSelect1" class="form-label">Select Artists</label>
                <select class="form-select" name="artist[]" id="foodstore" aria-label="Default select example" multiple>
                    <option selected value=" ">Select Artist</option>
                    @foreach($artist as $art)
                    <option value="{{$art->id}}">{{ $art->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-sm-4">
                <label for="exampleFormControlSelect1" class="form-label">Event Type <span class="required">*</span></label>
                <select class="form-select" id="event_type" aria-label="Default select example" name="event_type">
                    <option selected value=" ">Select Event Type</option>
                    <option value="0">Offline</option>
                    <option value="1">Online</option>
                    <option value="2">Both (online and offline)</option>
                </select>
            </div>
            <div class="mb-3 col-sm-4 offline_meeting">
                <label class="form-label" for="basic-default-phone">Venue <span class="required">*</span></label>
                <input type="text" id="basic-default-venue" class="form-control venue" name="venue" placeholder="Enter Destination" />
            </div>
            <div class="mb-3 col-sm-4 offline_meeting">
                <label class="form-label" for="basic-default-phone">Enter Address <span class="required">*</span></label>
                <input type="text" id="address" class="form-control venue" name="address" placeholder="Enter Address" />
            </div>
            <div class="mb-3 col-sm-4 offline_meeting">
                <label class="form-label" for="basic-default-phone">City <span class="required">*</span></label>
                <input type="text" id="city" class="form-control venue" name="city" placeholder="Enter City" />
            </div>
            <div class="mb-3 col-sm-4 offline_meeting">
                <label class="form-label" for="basic-default-phone">State <span class="required">*</span></label>
                <input type="text" id="state" class="form-control venue" name="state" placeholder="Enter State" />
            </div>
            <div class="mb-3 col-sm-4 offline_meeting">
                <label class="form-label" for="basic-default-phone">Country <span class="required">*</span></label>
                <input type="text" id="country" class="form-control venue" name="country" placeholder="Enter Country" />
            </div>
            <div class="mb-3 col-sm-4 offline_meeting">
                <label class="form-label" for="basic-default-postal_code">Postal Code <span class="required">*</span></label>
                <input type="text" id="postal_code" class="form-control venue" name="postal_code" placeholder="Enter Postal Code " />
            </div>
            <div class="mb-3 col-sm-4 offline_meeting">
                <label for="exampleFormControlSelect1" class="form-label">Food Store <span class="required">*</span></label>
                <select class="form-select" name="foodstore[]" id="foodstore" aria-label="Default select example" multiple>
                    <option selected value=" ">Select Food Store</option>
                    @foreach($foodstore as $data)
                    <option value="{{$data->id}}">{{ $data->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-sm-4 online_meeting">
                <label class="form-label" for="basic-default-postal_code">Meeting Link<span class="required">*</span></label>
                <input type="url" id="postal_code" class="form-control venue" name="meeting_link" placeholder="Enter Meeting Link " />
            </div>
            <div class="mb-3">
                Tickets<span class="required">*</span>
            </div>
            <div class="mb-3">
                <table class="table table-bordered" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>Ticket Category</th>
                            <th>Ticket Qty.</th>
                            <th>Ticket Price(per ticket)</th>
                            <th> <button type="button" name="add" id="add" class="btn btn-success">Add More</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableRow">
                        <tr>
                            <td><select name="ticket_cat[]" id="ticket_cat" class="form-control">
                                    <option value="" selected>Select Ticket Category</option>
                                    @foreach($ticket_catagory as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select></td>
                            <td><input type="text" name="ticket_qty[]" placeholder="Enter your Qty" class="form-control" /></td>
                            <td><input type="text" name="ticket_price[]" placeholder="Enter your Price" class="form-control" /></td>

                            <td><button type="button" class="btn btn-danger remove-tr">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 ">
                <label class="form-label" for="basic-default-message">About Event<span class="required">*</span></label>
                <textarea class="form-control content" name="about" placeholder="Description about the Event.."></textarea>
            </div>
            <div class="mb-3 col-sm-4">
                <label for="formFile" class="form-label">Image <span class="required">*</span></label>
                <input class="form-control" type="file" id="formFile" name="image" />
            </div>
            <div class="mb-3 col-sm-4">
                <label class="form-label" for="image">Event Images (Multiple)</label>
                <div class="input-group input-group-merge">
                    <input type="file" id="gallary_image" name="gallary_image[]" class="form-control" aria-describedby="image" multiple />
                    <span class="input-group-text" id="gallary_image">Image</span>
                </div>
            </div>
            <div class="mb-3 col-sm-4">
                <label class="form-label" for="basic-default-postal_code">Status</label>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" checked />
                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                </div>
            </div>
            <div class="mb-3">
                <input type="hidden" class="form-control" name="longitude" id="longitude" hidden />
                <input type="hidden" class="form-control" name="latitude" id="latitude" hidden />
            </div>
            <div class="mb-3 col-sm-4">
                <button type=" submit" class="btn btn-primary">Save and Back</button>
            </div>
        </div>
    </form>
</section>



<div class="modal fade" id="scroll-modal" tabindex="-1" role="dialog" aria-labelledby="scroll-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scroll-modal-label">Add Artist </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form action="" method="POST" id="regForm" name="formPrevent" enctype="multipart/form-data">

                        <div class="row">
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="name">Artist Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="name">Nick Name </label>
                                <input type="text" class="form-control" name="nick_name" id="nick_name" />
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
                                <label class="form-label" for="bio">Bio <span class="required">*</span></label>
                                <input type="text" class="form-control" name="bio" id="bio" placeholder="Bio" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="country">Country <span class="required">*</span></label>
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="itemvideo">Artist Videos</label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="video" name="video" class="form-control" aria-describedby="video" multiple />
                                    <span class="input-group-text" id="video">Videos</span>
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Profile Image <span class="required">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="file" name="image" id="image" class="form-control" aria-describedby="image" />
                                    <span class="input-group-text" id="image">Image</span>
                                </div>
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Artist Images(Gallery)</label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="galaey_image" name="gallary_images[]" class="form-control" aria-describedby="image" multiple />
                                    <span class="input-group-text" id="gallary_image">Image</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-sm-4">
                                    <label class="form-label" for="expertize">Expertize <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="expertize" id="expertize" placeholder="Expertize" />

                                    <a class="mb-3 col-sm-4" id="duplicate-btn">Add More</a>
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" checked />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Create</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $("#regForm").validate({
            rules: {
                title: "required",
                email: "required",
                contact_number: {
                    required: true,
                    number: "Enter Number only",
                },
                start_date: "required",
                end_date: "required",
                start_time: "required",
                end_time: "required",
                duration: "required",
                category_id: "required",
                'artist[]': "required",
                description: "required",
                'ticket_cat[]': "required",
                'ticket_qty[]': "required",
                'ticket_price[]': "required",
                foodstore: "required",
                about: "required",
                image: "required",
            },
        });
    });
</script>
<script type="text/javascript">
    var Data_to_clone = $('#tableRow').html();
    $("#add").click(function() {
        $("#tableRow").append(Data_to_clone);
        $('.remove-tr').css('display', 'block');
    });

    $(document).on('click', '.remove-tr', function() {
        $(this).parents('tr').remove();
    });

    $("#event_type").change(function() {
        var event_types = $('#event_type').val();
        if (event_types == 0) {
            $('.offline_meeting').css('display', 'block')
            $('.online_meeting').css('display', 'none');
        }
        if (event_types == 1) {
            $('.offline_meeting').css('display', 'none')
            $('.online_meeting').css('display', 'block');
        }
        if (event_types == 2) {
            $('.offline_meeting').css('display', 'block')
            $('.online_meeting').css('display', 'block');
        }
    })

    $("#start_time, #end_time").change(function() {
        var start_time = $('#start_time').val();
        var end_time = $('#end_time').val();

        if (start_time && end_time) {
            var start_ti = new Date("1970-1-1 " + start_time)
            var end_ti = new Date("1970-1-1 " + end_time);
            if (start_ti <= end_ti) {
                var diff = (end_ti - start_ti) / 1000 / 60;
                var hour = parseInt(diff / 60);
                var day = parseInt(hour / 24);
                var min = diff % 60;
                if (min < 10) {
                    min = "0" + min;
                }

                if (hour < 10) {
                    hour = "0" + hour;
                }
                if (day >= 1) {
                    $('#duration_id').val(day + ":" + hour + ":" + min)
                }
                $('#duration_id').val(hour + ":" + min)
            }
        }
    })


    // Artist Modal 
    $(document).ready(function() {
        $("#myModal").on("show.bs.modal", function() {
            $("body").addClass("modal-open");
        }).on("hide.bs.modal", function() {
            $("body").removeClass("modal-open");
        });
    });


    var duplicateBtn = document.getElementById("duplicate-btn");
    var originalInput = document.getElementById("expertize");

    duplicateBtn.onclick = function() {
        var newInput = originalInput.cloneNode(true);
        newInput.setAttribute("id", "input" + (document.querySelectorAll(".form-control").length + 1));
        newInput.setAttribute("name", "input" + (document.querySelectorAll(".form-control").length + 1));
        var removeBtn = document.createElement("button");
        removeBtn.innerHTML = "Remove";
        removeBtn.onclick = function() {
            newInput.parentNode.removeChild(newInput);
            removeBtn.parentNode.removeChild(removeBtn);
        };
        originalInput.parentNode.insertBefore(removeBtn, duplicateBtn);
        originalInput.parentNode.insertBefore(newInput, duplicateBtn);
    };
    ClassicEditor
        .create(document.querySelector('.content'))
        .catch(error => {
            console.error(error);
        });
</script>

<style>
    .header22 a {
        float: right;
    }

    .remove-tr {
        display: none;
        /* background-color: #b02a37; */
    }

    .online_meeting {
        display: none;
    }
</style>

@endsection('content')