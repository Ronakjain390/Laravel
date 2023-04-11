@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Events</h5>
                    <a href="{{ route('Events') }}" class="btn rounded-pill btn-primary">Back</a>
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
                    <form action="{{ route('StoreEvents') }}" id="regForm" method="POST" enctype="multipart/form-data">
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
                                <label for="" class="col-md-4 col-form-label">Start Date <span class="required">*</span></label>
                                <input class="form-control datepicker" type="text" name="start_date" />
                            </div>
                            <div class="col-sm-4">
                                <label for="html5-date-input" class="col-md-3 col-form-label">End Date <span class="required">*</span></label>
                                <input class="form-control datepicker" type="text" id="end_date" name="end_date" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Start Time<span class="required">*</span></label>
                                <input type="time" id="start_time" class="form-control " name="start_time" placeholder="Enter Start Time " />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">End Time<span class="required">*</span></label>
                                <input type="time" id="end_time" class="form-control" name="end_time" placeholder="Enter End Time  " />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Duration<span class="required">*</span></label>
                                <input type="time" id="duration_id" class="form-control" name="duration" placeholder="Enter Duration of event " />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="exampleFormControlSelect1" class="form-label">Category <span class="required">*</span></label>
                                <select class="form-select" name="category_id" id="category_id" aria-label="Default select example">
                                    <option selected value=" ">Select Event Category</option>
                                    @foreach($eventCategoryData as $data)
                                    <option value="{{$data->id}}">{{ $data->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="exampleFormControlSelect1" class="form-label">Select Artists<span class="required">*</span></label>
                                <select class="form-select" name="artist[]" id="artist" aria-label="Default select example" multiple>
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
                            <div class="mb-3 col-sm-4 venue">
                                <label class="form-label" for="basic-default-phone">Venue <span class="required">*</span></label>
                                <input type="text" id="basic-default-venue" class="form-control venue" name="venue" placeholder="Enter Destination" />
                            </div>
                            <!-- <div class="mb-3 col-sm-4">
                <label class="form-label" for="basic-default-price">Price <span class="required">*</span></label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text">₹</span>
                    <input type="text" class="form-control" name="price" placeholder="100" aria-label="Amount" />
                </div>
            </div>
            <div class="mb-3 col-sm-4">
                <label class="form-label" for="basic-default-postal_code">Time<span class="required">*</span></label>
                <input type="time" id="postal_code" class="form-control" name="time" placeholder="Enter Time Required" />
            </div> -->
                            <div class="mb-3 col-sm-4 address">
                                <label class="form-label" for="basic-default-phone">Enter Address <span class="required">*</span></label>
                                <input type="text" id="address" class="form-control " name="address" placeholder="Enter Address" />
                            </div>
                            <div class="mb-3 col-sm-4 city">
                                <label class="form-label" for="basic-default-phone">City <span class="required">*</span></label>
                                <input type="text" id="city" class="form-control " name="city" placeholder="Enter City" />
                            </div>
                            <div class="mb-3 col-sm-4 state">
                                <label class="form-label" for="basic-default-phone">State <span class="required">*</span></label>
                                <input type="text" id="state" class="form-control " name="state" placeholder="Enter State" />
                            </div>
                            <div class="mb-3 col-sm-4 country">
                                <label class="form-label" for="basic-default-phone">Country <span class="required">*</span></label>
                                <input type="text" id="country" class="form-control " name="country" placeholder="Enter Country" />
                            </div>
                            <div class="mb-3 col-sm-4 postal_code">
                                <label class="form-label" for="basic-default-postal_code">Postal Code <span class="required">*</span></label>
                                <input type="text" id="postal_code" class="form-control " name="postal_code" placeholder="Enter Postal Code " />
                            </div>
                            <div class="mb-3 col-sm-4 offline_meeting">
                                <label for="exampleFormControlSelect1" class="form-label">Food Store <span class="required">*</span></label>
                                <select class="form-select foodstore" name="foodstore[]" id="foodstore" aria-label="Default select example" multiple>
                                    <option selected value=" ">Select Food Store</option>
                                    @foreach($foodstore as $data)
                                    <option value="{{$data->id}}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-sm-4 online_meeting">
                                <label class="form-label" for="basic-default-postal_code">Online Event Link<span class="required">*</span></label>
                                <input type="url" class="form-control" name="meeting_link" placeholder="Online Event Link " />
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
                                <button type=" submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script type="text/javascript">
    var Data_to_clone = $('#tableRow').html();
    // console.log(Data_to_clone, "hello")
    $("#add").click(function() {
        $("#tableRow").append(Data_to_clone);
        $('.remove-tr').css('display', 'block');
    });

    $(document).on('click', '.remove-tr', function() {
        $(this).parents('tr').remove();
    });

    $("#event_type").change(function() {
        var event_types = $('#event_type').val();
        // console.log(event_types, "hello")
        if (event_types == 0) {
            $('.offline_meeting').css('display', 'block')
            $('.online_meeting').css('display', 'none');
            $('.venue').css('display', 'block ');
            $('.address').css('display', 'block ');
            $('.city').css('display', 'block ');
            $('.state').css('display', 'block ');
            $('.country').css('display', 'block ');
            $('.postal_code').css('display', 'block ');

        }
        if (event_types == 1) {
            $('.offline_meeting').css('display', 'none')
            $('.venue').css('display', 'none');
            $('.online_meeting').css('display', 'block');
            $('.address').css('display', 'none ');
            $('.city').css('display', 'none ');
            $('.state').css('display', 'none ');
            $('.country').css('display', 'none ');
            $('.postal_code').css('display', 'none ');
        }
        if (event_types == 2) {
            $('.offline_meeting').css('display', 'block')
            $('.online_meeting').css('display', 'block');
            $('.venue').css('display', 'block ');
            $('.address').css('display', 'block ');
            $('.city').css('display', 'block ');
            $('.state').css('display', 'block ');
            $('.country').css('display', 'block ');
            $('.postal_code').css('display', 'block ');
        }
    })
    $("#start_time, #end_time").change(function() {
        var start_time = $('#start_time').val();
        var end_time = $('#end_time').val();

        // console.log(typeof(start_time), typeof(end_time), "hello")
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
                // alert(hour + ":" + min);
            }
        }
    })
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

@push('js')
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
            },

        });

    });
</script>
@endpush
@endsection('SuperAdminContent')