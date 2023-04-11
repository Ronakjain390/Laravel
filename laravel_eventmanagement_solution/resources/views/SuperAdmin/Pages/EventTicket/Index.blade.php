@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Event Tickets</h5>
            <a href="{{ route('CreateEventTicket') }}" class="btn rounded-pill btn-primary">Create</a>
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
                        <th>Ticket Name</th>
                        <th>Total Tickets</th>
                        <th>Coupons</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Age</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($eventTicketData as $index=>$eventTicket)
                    <tr>
                        <td> {{ $index+1 }} </td>
                        <td>
                            <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up me-2" title="Lilian Fuller">
                                    {{$eventTicket->ticket_name}}
                                </li>
                            </ul>
                        </td>
                        <td>{{ $eventTicket->total_tickets }}</td>
                        <td> {{ $eventTicket->coupons }}</td>
                        <td> {{ $eventTicket->start_date }} </td>
                        <td> {{ $eventTicket->end_date }} </td>
                        <td> {{ $eventTicket->age }} </td>
                        <td> {{ $eventTicket->price }} </td>

                        <td>
                            @if($eventTicket->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>

                            @else
                            <span class="badge bg-label-warning me-1">InActive</span>
                        </td>
                        @endif


                        <td>
                            
                            <a href="{{ route('ShowEventTicket', ['id' => $eventTicket->id ]) }}"><i class="bx bx-show-alt me-1"></i></a>
                            <a  href=" {{ route('EditEventTicket', ['id' => $eventTicket->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteEventTicket', ['id' => $eventTicket->id ]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')