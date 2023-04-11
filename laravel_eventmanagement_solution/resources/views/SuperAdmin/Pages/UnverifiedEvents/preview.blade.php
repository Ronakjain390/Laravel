@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Events Details</h5>
            <a href="{{ route('Events') }}" class="btn rounded-pill btn-primary">Back</a>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-md mb-4 mb-md-0">
                    <div class="accordion mt-3" id="accordionExample">
                        <div class="card accordion-item active">
                            <h2 class="accordion-header" id="headingOne">
                                <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                                    <h4> Event Detail</h4>
                                </button>
                            </h2>

                            <div id="accordionOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label><b>Event Name</b></label>
                                            <div class="mb-2 text">
                                                {{ $event->title }}
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> Email </b>
                                            <div class="mb-2 text">{{ $event->email }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> Contact Number </b>
                                            <div class="mb-2 text">{{ $event->contact_number }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> Start Date </b>
                                            <div class="mb-2 text">{{date('D, d M Y', strtotime($event->start_date)) }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> End Date </b>
                                            <div class="mb-2 text">{{date('D, d M Y', strtotime($event->end_date)) }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b>Start Time </b>
                                            <div class="mb-2 text">{{ $event->start_time }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> End Time </b>
                                            <div class="mb-2 text">{{ $event->end_time }}</div>
                                        </div>
                                        @if(!is_null($event->duration))
                                        <div class="col-sm-4">
                                            <b> Event Duration</b>
                                            <div class="mb-2 text">{{ $event->duration }}</div>
                                        </div>
                                        @endif
                                        <div class="col-sm-4">
                                            <b> Event Category</b>
                                            <div class="mb-2 text">{{ $eventCategory->category_name }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> Event Type</b>
                                            @if($event->event_type==0)
                                            <div class="mb-2 text">Offline</div>
                                            @elseif($event->event_type==1)
                                            <div class="mb-2 text">Online</div>
                                            @elseif($event->event_type==2)
                                            <div class="mb-2 text">Both(Online and Offline)</div>
                                            @endif
                                        </div>
                                        @if($event->event_type==0 || $event->event_type==2)
                                        <div class="col-sm-4">
                                            <b> Venue </b>
                                            <div class="mb-2 text">{{ $event->venue }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> Address </b>
                                            <div class="mb-2 text">{{ $event->address }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> City </b>
                                            <div class="mb-2 text">{{ $event->city }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> State </b>
                                            <div class="mb-2 text">{{ $event->state }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> Country </b>
                                            <div class="mb-2 text">{{ $event->country }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <b> Postal Code </b>
                                            <div class="mb-2 text">{{ $event->postal_code }}</div>
                                        </div>
                                        @endif

                                        @if($event->event_type==1 || $event->event_type==2)
                                        <div class="col-sm-4">
                                            <b> Meeting Link</b>
                                            <div class="mb-2 text">{{ $event->meeting_link }}</div>
                                        </div>
                                        @endif
                                        <div class="col-sm-4">
                                            <b>Status</b>
                                            <div class="status">
                                                @if($event->status == 1)
                                                <span class="badge bg-label-primary me-1">ACTIVE</span>
                                                @else
                                                <span class="badge bg-label-warning me-1">INACTIVE</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class=" col-label-form"><b>Image</b></label>
                                            <div>
                                                <img src="{{ asset('Assets/images/' . $event->image) }}" style="height:120px; width:200px" />
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <b> About </b>
                                            <div class="mb-2 text">{!! $event->about !!}</div>
                                        </div>

                                        <div class="col-sm-4">
                                            @if(is_array(json_decode($event->gallary_images)))
                                            <?php
                                            foreach ($a = json_decode($event->gallary_images) as $index => $a1); ?>
                                            <label class=" col-label-form"><b>Gallery </b></label>

                                            <div class="col-sm-6">
                                                <div class="scroller-height border">
                                                    <?php for ($i = 0; $i < count($a); $i++) { ?>
                                                        <a class="img ms-4">
                                                            <img src="{{asset('Assets/images/' .$a1)}}" id="image-<?php echo $index; ?>" onclick="showLargeImage('<?php echo 'Assets/images/' . $a1 ?>', 'image-<?php echo $index; ?>')" alt="" height="70px" width="70px">
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            @endif

                                        </div>

                                    </div>
                                </div>
                            </div>
                            @if(count($eventArtist)>0)
                            <div class="card accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionTwo" aria-expanded="false" aria-controls="accordionTwo">
                                        <h4>Artist</h4>
                                    </button>
                                </h2>
                                <div id="accordionTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">

                                        <div>

                                        </div>
                                        @foreach($eventArtist as $art)
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <b> Name </b>
                                                <div class="mb-2 text">{{ $art->name}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> Nick Name </b>
                                                <div class="mb-2 text">{{ $art->nick_name}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> Email </b>
                                                <div class="mb-2 text">{{ $art->email}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> Contact Number </b>
                                                <div class="mb-2 text">{{ $art->contact_number}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> Country </b>
                                                <div class="mb-2 text">{{ $art->country}}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(count($eventFoodStore)>0)
                            <div class="card accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionThree" aria-expanded="false" aria-controls="accordionThree">
                                        <h4>Food Stores</h4>
                                    </button>
                                </h2>
                                <div id="accordionThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">



                                        @foreach($eventFoodStore as $food)
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <b> Food Store Name </b>
                                                <div class="mb-2 text">{{ $food->name}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> Email </b>
                                                <div class="mb-2 text">{{ $food->email}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> Contact Number </b>
                                                <div class="mb-2 text">{{ $food->contact_number}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> State </b>
                                                <div class="mb-2 text">{{ $food->state}}</div>
                                            </div>
                                            <div class="col-sm-2">
                                                <b> Country </b>
                                                <div class="mb-2 text">{{ $food->country}}</div>
                                            </div>
                                        </div>
                                        @endforeach


                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(count($eventTicket)>0)
                            <div class="card accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionFour" aria-expanded="false" aria-controls="accordionFour">
                                        <h4>Tickets</h4>
                                    </button>
                                </h2>
                                <div id="accordionFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        @foreach($eventTicket as $ticket)
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <b> Ticket Category </b>
                                                @foreach($tickets as $tick)
                                                @if($tick->id == $ticket->ticket_category_id)
                                                <div class="mb-2 text">{{ $tick->name}}</div>
                                                @endif
                                                @endforeach
                                            </div>
                                            <div class="col-sm-3">
                                                <b> No. of Tickets </b>
                                                <div class="mb-2 text">{{ $ticket->no_of_ticket}}</div>
                                            </div>
                                            <?php
                                            $currency = currency();
                                            $currency_symbol = $currency[0]->currency_symbol;
                                            ?>
                                            <div class="col-sm-3">
                                                <b> Ticket Price </b>
                                                <div class="mb-2 text">{{$currency[0]->currency_symbol}}{{ $ticket->ticket_price}}</div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>

        </section>
    </div>
    <style>
        .scroller-height {
            height: 300px;
            width: 500px;
            overflow-y: auto;
        }

        .scroller-height.border {
            padding: 10px 10px 10px 15px;
        }
    </style>
    @endsection('SuperAdminContent')