@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
@if (Session::has('success'))
<div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
    {{ Session::get('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif


<div class="container card p-4">
    <table>
        @if(!$bookingTicket->isEmpty())
        @foreach($bookingTicket->keys() as $depart)
        <tbody>
            <tr>
                <td >
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
        @else
        <div>
            No Ticket Booked
        </div>
        @endif
    </table>

</div>
@endsection('content')