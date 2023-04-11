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
                <div class="card p-4">
                    <table>
                        @foreach($bookingTicket->keys() as $depart)
                        <tbody>
                            <tr>
                                <td>
                                    @foreach($category as $category1)
                                    @if($depart == $category1->id)

                                    <b class="mb-10"> Category : {{$category1->name}}</b> <br>

                                    <?php
                                    $tickets = [];
                                    foreach ($bookingTicket[$depart] as $tick) {
                                        array_push($tickets, $tick->quantity);
                                    }
                                    $total_ticks = array_sum($tickets);
                                    ?>
                                    @foreach($totalTicket as $totalTicket1)
                                    @if($totalTicket1->ticket_category_id == $category1->id )
                                    Total Tickets : {{ $totalTicket1->no_of_ticket }}
                                    <br>
                                    Available Tickets :{{ $totalTicket1->no_of_ticket - $total_ticks }} <br>
                                    Booked Tickets :{{$total_ticks}} <br> <br>
                                    @endif
                                    @endforeach
                                    @endif
                                    @endforeach
                                </td>
                                <td></td>
                            </tr>

                        </tbody>

                        @endforeach
                    </table>

                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')