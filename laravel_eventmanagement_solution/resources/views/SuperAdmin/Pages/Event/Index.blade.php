<?php

$currency = currency();
if (isset($currency[0]->currency_symbol)) {

    $currency_symbol = $currency[0]->currency_symbol;
}

?>
@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Events</h5>
            <a href="{{ route('CreateEvents') }}" class="btn rounded-pill btn-primary">Create</a>
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
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Event Type</th>
                        <th>Event Category</th>
                        <th>Start Date</th>
                        <!-- <th>Available Tickets</th> -->
                        <th>Status</th>
                        <th>City</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($eventData as $index=>$eventData)
                    <tr>
                        <td> {{ $index+1 }}<i class="fab fa-angular fa-lg text-danger "></i> <strong></strong></td>
                        <td>{{ $eventData->title }} </td>
                        <td> @if($eventData->event_type == '0')
                            Online
                            @elseif($eventData->event_type == '1')
                            Offline
                            @endif </td>

                        <td> @foreach($eventCategoryData as $eventCategoryData1)
                            @if($eventCategoryData1->id == $eventData->category_id)
                            {{ $eventCategoryData1->category_name }}
                            @endif
                            @endforeach
                        </td>
                        <td>{{date('D, d M Y', strtotime($eventData->start_date ))}}</td>
                        <!-- <td>
                            @foreach($tickets as $tickets1)
                            @if($tickets1->event_id == $eventData->id)
                            {{$tickets1->available_tickets}}
                            @endif
                            @endforeach
                        </td> -->

                        <td> @if($eventData->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>

                            @else
                            <span class="badge bg-label-warning me-1">InActive</span>
                        </td>
                        @endif</td>
                        <td>{{ $eventData->city }}</td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('TicketShowEvents', $eventData->id) }}"><i class="bi bi-ticket-detailed"></i></a>
                            <a href="{{ route('show_event', ['id' => $eventData->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                            @if($user->id == $eventData->user_id)
                            <a href="{{ route('EditEvents', ['id' => $eventData->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteEvents', ['id' => $eventData->id ]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                            @else
                            <a href="{{ route('Un_Verify', ['id' => $eventData->id ]) }}" onclick="return confirm('Are you sure you want to un-verify this event?')"><i class="bx bx-block me-1"></i> </a>
                            <a href="{{ route('delete_event', $eventData->id) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i></a>

                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')