@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="category-browse--header">
    <div class="category-browse--header-">
        <div class="container">
            <div class="category-browse--header-text">
                <div class="category-browse--header-text__wrapper">
                    <h1 class="category-browse__header--content">Ticket Details <div class="eds-text-bl" style="color:#FFF58C;padding-top:8px"></div>
                    </h1>
                    <p></p>
                </div>
            </div>
            <!-- <aside class="category-browse--header-image category-browse--header-image--square"><img fetchpriority="high" class="full-width-img" loading="eager" src="" alt="[object Object]"></aside> -->
        </div>
    </div>
</div>
<div class="container">
    <div class="card-header d-flex " style="justify-content: space-between">
        <h5>Ticket </h5>
        <a href="{{ route('TicketListing') }}" class="btn rounded-pill btn-primary">Back</a>
    </div><br>
    <table class="table" style="width:100%;background:lightgoldenrodyellow">
        <thead>
            <tr>
                <th>Sr.No</th>
                <th>Total Tickets</th>
                <th>Price</th>
                <th>Fee</th>
                <th>Total Amount</th>
                <!-- <th>Status</th> -->
                <th>Cancel Ticket</th>
            </tr>
        </thead>

        @foreach($ticket->keys() as $depart)
        <tbody>
            <tr>
                <td colspan="4">@foreach($category as $department1)
                    @if($depart == $department1->id)
                    <p><b>{{$department1->name}}</b></p>
                    @endif
                    @endforeach
                </td>
            </tr>
            @foreach($ticket[$depart] as $index=>$job)
            <!-- model value -->
            <div id="myModel" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cancel Ticket</h5>
                        </div>
                        <div class="modal-body">
                            <div>
                                <h6>Do You Want to cancel tickets ?</h6>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="deleteButton" onclick="closeModel()" class="btn btn-danger text-white">Close</button>
                            <a href="{{route('cancelTicket', ['id' =>$job->id ])}}" class="btn btn-primary">Yes, cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- model value -->
            <tr>
                <td>{{ $index+1 }}.</td>
                <td>{{$job->quantity}} <br>
                </td>
                <td>{{$job->price}} <br>
                </td>
                <td> {{$job->fee}} <br>
                </td>
                <td> {{$job->price + $job->fee}}<br>
                </td>

                <!-- <td>{{$job->status}} <br> -->
                </td>
                <td>
                    <button type="button" class="btn btn-primary" {{ $job->status === 'Cancelled' ? 'disabled' : ''}}>
                        <a onclick="togglemodel()">Cancel Ticket</a>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
        @endforeach
    </table>
</div>

<!-- Button trigger modal -->
<!-- Modal -->

<script type="text/javascript">
    function togglemodel() {
        let mod = document.getElementById('myModel').style.display = "block"
    }

    function closeModel() {
        let clos = document.getElementById('myModel').style.display = "none"
    }
</script>

@endsection('UserContent')