@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Events</h5>
                    <a href="{{ route('Events') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('update_events', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-title">Title <span class="required">*</span></label>
                                <input type="text" class="form-control" value="{{ $event->title }}" id="basic-default-title" placeholder="Title" name="title" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-email">Email <span class="required">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="text" id="basic-default-email" value="{{ $event->email }}" class="form-control" placeholder="Email" name="email" aria-label="john.doe" aria-describedby="basic-default-email2" />
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">Contact Number <span class="required">*</span></label>
                                <input type="text" id="basic-default-phone" value="{{ $event->contact_number }}" class="form-control phone-mask" name="contact_number" placeholder="Contact Number" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="html5-date-input" class="col-md-3 col-form-label">Start Date <span class="required">*</span></label>
                                <input class="form-control datepicker" value="{{ $event->start_date }}" type="text" name="start_date" id="html5-date-input" />
                            </div>
                            <div class="col-sm-4">
                                <label for="html5-date-input" class="col-md-3 col-form-label">End Date <span class="required">*</span></label>
                                <input class="form-control datepicker" value="{{ $event->end_date }}" type="text" id="html5-date-input" name="end_date" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Start Time<span class="required">*</span></label>
                                <input type="time" id="start_time" class="form-control" name="start_time" placeholder="Enter Start Time " value="{{ $event->start_time }}" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">End Time<span class="required">*</span></label>
                                <input type="time" id="end_time" class="form-control" name="end_time" placeholder="Enter End Time" value="{{ $event->end_time }}" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Duration<span class="required">*</span></label>
                                <input type="time" id="duration" class="form-control" name="duration" placeholder="Enter Duration of event " value="{{ $event->duration }}" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="exampleFormControlSelect1" class="form-label">Category <span class="required">*</span></label>
                                <select class="form-select" name="category_id" id="exampleFormControlSelect1" aria-label="Default select example">
                                    <option selected value="{{ $eventCategory->id }}">{{ $eventCategory->category_name }}</option>
                                    <div class="dropdown-divider">
                                    </div>
                                    @foreach($eventCategoryAll as $data)
                                    <option value="{{$data->id}}">{{ $data->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="exampleFormControlSelect1" class="form-label">Event Type <span class="required">*</span></label>
                                <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example" name="event_type">
                                    <option value="0" {{$event->event_type == 0 ? 'Selected' : ''}}>Offline</option>
                                    <option value="1" {{$event->event_type == 1 ? 'Selected' : ''}}>Online</option>
                                    <option value="2" {{$event->event_type == 2 ? 'Selected' : ''}}>Both(Online and Offline)</option>
                                </select>
                            </div>

                            @if($event->event_type==0 || $event->event_type==2)
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">Venue <span class="required">*</span></label>
                                <input type="text" id="basic-default-venue" value="{{ $event->venue }}" class="form-control venue" name="venue" placeholder="Enter Destination" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">Address <span class="required">*</span></label>
                                <input type="text" id="address" value="{{ $event->address }}" class="form-control venue" name="address" placeholder="Enter Address" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">City <span class="required">*</span></label>
                                <input type="text" id="city" value="{{ $event->city }}" class="form-control venue" name="city" placeholder="Enter City" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">State <span class="required">*</span></label>
                                <input type="text" id="state" value="{{ $event->state }}" class="form-control venue" name="state" placeholder="Enter State" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">Country <span class="required">*</span></label>
                                <input type="text" id="country" value="{{ $event->country }}" class="form-control venue" name="country" placeholder="Enter Country" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Postal Code <span class="required">*</span></label>
                                <input type="text" id="postal_code" value="{{ $event->postal_code }}" class="form-control venue" name="postal_code" placeholder="Enter Postal Code " />
                            </div>
                            <input type="hidden" class="form-control" name="longitude" id="longitude" hidden value="{{$event->longitude}}" />
                            <input type="hidden" class="form-control" name="latitude" id="latitude" hidden value="{{$event->latitude}}" />
                            @endif

                            @if($event->event_type == 1 || $event->event_type ==2)
                            <div class="mb-3 col-sm-4 online_meeting">
                                <label class="form-label" for="basic-default-postal_code">Online Event Link<span class="required">*</span></label>
                                <input type="url" class="form-control" name="meeting_link" placeholder="Online Event Link " value="{{ $event->meeting_link }}" />
                            </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-message">About Event<span class="required">*</span></label>
                                <textarea class="form-control content" name="about" placeholder="Description about the Event..."> {!! $event->about !!} </textarea>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="formFile" class="form-label">Image <span class="required">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" />
                                <input type="hidden" name="hidden_Image" value="{{ $event->image }}" />

                                <img src="{{ asset('Assets/images/'.$event->image) }}" alt="" width="150" height="100">
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="image">Portfolio Images(Multiple) <span class="required">(Upload Multiple)</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="gallary_image" name="multiple_image[]" class="form-control" aria-describedby="image" multiple />
                                    <span class="input-group-text" id="gallary_image">Image</span>
                                </div>
                                @if(is_array(json_decode($event->gallary_images)))
                                <?php $a = json_decode($event->gallary_images); ?>
                                <?php for ($i = 0; $i < count($a); $i++) { ?>
                                    <a class="img-pop-up ms-4">
                                        <img class="single-gallery-image" src="{{asset('Assets/images/'.$a[$i]) }}" style="height:150px; width:150px" />
                                    </a>
                                <?php } ?>
                                @endif
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" {{ $event->status=='1' ? 'checked':'' }} />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>

                            @if($event->event_type == 0 ||$event->event_type == 2)
                            <div class="mt-3 row">
                                <div class="col-md-10">
                                    <h3><b>Food Stores</b></h3>
                                </div>
                                <div class="col-md-2"><a class="btn btn-success" onclick="togglemodel()">Add Food Store</a></div>
                            </div>

                            <!-- model value  -->
                            <div id="myModel" class="modal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add Food Store</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <label for="user"><b>Food Stores</b></label><br>
                                                <select class="form-control" name="food_store_id[]" id="userId" multiple>
                                                    <option value=" ">--Select Food Store--</option>
                                                    @foreach($food_store as $food)
                                                    <option value="{{ $food->id }}">{{$food->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id="deleteButton" onclick="closeModel()" class="btn btn-danger text-white">Close</button>
                                            <button type="button" id="savebutton" onclick="closeModel()" class="btn btn-secondary">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- model value  -->

                            @if(count($eventFoodStore)>0)

                            @foreach($eventFoodStore as $food)
                            <!-- <input type="text" name="food_store_id[]" value="{{ $food->id }}" hidden> -->
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Food Store Name<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$food->name}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Email<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$food->email}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Conatct Number<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$food->contact_number}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-2">
                                <label for="exampleFormControlSelect1" class="form-label">Country<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$food->country}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-1">
                                <label for="exampleFormControlSelect1" class="form-label">Delete</label> <br>
                                <a href="{{ route('food_store_delete', $food->id) }}"><i class="bx bxs-trash"></i></a>
                            </div>
                            @endforeach
                            @endif

                            @endif

                            <div class="mt-3 row">
                                <div class="col-md-10">
                                    <h3><b>Artist</b></h3>
                                </div>
                                <div class="col-md-2">
                                    <div><a class="btn btn-success" onclick="togglemodel1()">Add Artist</a></div>
                                </div>
                            </div>
                            <!-- model value  -->
                            <div id="myModel1" class="modal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add Artist</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <label for="user"><b>Add Artist</b></label><br>
                                                <select class="form-control" name="artist_id[]" id="userId" multiple>
                                                    <option value=" ">--Select Artist--</option>
                                                    @foreach($artist as $artist1)
                                                    <option value="{{ $artist1->id }}">{{$artist1->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id="deleteButton" onclick="closeModel1()" class="btn btn-danger text-white">Close</button>
                                            <button type="button" id="savebutton" onclick="closeModel1()" class="btn btn-secondary">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- model value  -->
                            @if(count($eventArtist)>0)
                            @foreach($eventArtist as $art)
                            <input type="text" hidden value="{{$art->id}}" name="artist_id[]">
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Artist Name<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$art->name}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Email<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$art->email}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Contact Number<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$art->contact_number}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-2">
                                <label for="exampleFormControlSelect1" class="form-label">Country<span class="required">*</span></label>
                                <input type="text" name="name" value="{{$art->country}}" class="form-control" readonly>
                            </div>
                            <div class="mb-3 col-sm-1">
                                <label for="exampleFormControlSelect1" class="form-label">Delete</label> <br>
                                <a href="{{ route('artist_delete',$art->id ) }}"><i class="bx bxs-trash"></i></a>
                            </div>
                            @endforeach
                            @endif

                            <div class="mt-3 row">
                                <div class="col-md-10">
                                    <h3><b>Events Ticket</b></h3>
                                </div>
                                <div class="col-md-2">
                                    <div><button type="button" name="add" id="add" class="btn btn-success">Add Ticket Cat.</button></div>
                                </div>
                            </div>
                            @if(count($eventTicket)>0)
                            @foreach($eventTicket as $ticket)
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Event Ticket Cat. <span class="required">*</span></label>
                                <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example" name="ticket_cat[]">
                                    @foreach($tickets as $tick)
                                    @if($tick->id == $ticket->ticket_category_id)
                                    <option value="{{$tick->id }}" selected>{{$tick->name}}</option>
                                    @else
                                    <option value="{{$tick->id }}">{{$tick->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">No. of Tickets<span class="required">*</span></label>
                                <input type="text" name="ticket_qty[]" value="{{$ticket->no_of_ticket}}" class="form-control">
                            </div>

                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Ticket Price<span class="required">*</span></label>
                                <input type="text" name="ticket_price[]" value="{{$ticket->ticket_price}}" class="form-control">
                            </div>
                            <div class="mb-3 col-sm-3">
                                <label for="exampleFormControlSelect1" class="form-label">Delete</label><br>
                                <a href="{{ route('ticket_delete', $ticket->id) }}"><i class="bx bxs-trash"></i></a>
                            </div>
                            @endforeach
                            @endif

                            <div class="mb-3 ticket_cat_table">
                                <table class="table table-bordered" id="dynamicTable">
                                    <thead>
                                        <tr>
                                            <th>Ticket Category</th>
                                            <th>Ticket Qty.</th>
                                            <th>Ticket Price(per ticket)</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableRow">
                                        <tr>
                                            <td><select name="ticket_cat[]" id="ticket_cat" class="form-control">
                                                    <option value="" selected>Select Ticket Category</option>
                                                    @foreach($ticket_cat as $cat)
                                                    <option value="{{ $cat->id }}">{{$cat->name }}</option>
                                                    @endforeach
                                                </select></td>
                                            <td><input type="text" name="ticket_qty[]" placeholder="Enter your Qty" class="form-control" /></td>
                                            <td><input type="text" name="ticket_price[]" placeholder="Enter your Price" class="form-control" /></td>

                                            <td><button type="button" class="btn btn-danger remove-tr">Remove</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save and Back</button>
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
    var click = 0;
    $("#add").click(function() {
        click = click + 1;
        if (click == 1) {
            $('.ticket_cat_table').css('display', 'block');
            $('.remove-tr').css('display', 'block');
        } else {
            $("#tableRow").append(Data_to_clone);
            $('.remove-tr').css('display', 'block');
        }
    });

    $(document).on('click', '.remove-tr', function() {
        $(this).parents('tr').remove();
    });

    function togglemodel() {
        let mod = document.getElementById('myModel').style.display = "block"
    }

    function closeModel() {
        let clos = document.getElementById('myModel').style.display = "none"
    }

    function togglemodel1() {
        let mod = document.getElementById('myModel1').style.display = "block"
    }

    function closeModel1() {
        let clos = document.getElementById('myModel1').style.display = "none"
    }
</script>

<style>
    .ticket_cat_table {
        display: none;
    }
</style>
@endsection('SuperAdminContent')