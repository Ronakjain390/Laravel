@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<div class="container-fluid bg-secondary mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
        <br><br>
    </div>
</div>

<div class="container">
    <div class="card">

        <div class="card-body">
            <form method="GET" action="{{ route('addTicket', ['id' => $eventId ])}}" enctype="multipart/form-data">
                @csrf
                <div>
                    <h6>
                        Select the ticket
                    </h6>
                    <input id=counter type=number value="0" min="0" name="tickets">
                    <button type="button" min="0" id="increment-btn">+</button>
                    <button type="button" id="decrement-btn">-</button>

                </div>
                <div class="mb-3 col-sm-4">
                    <label for="exampleFormControlSelect1" class="form-label">Event Type <span class="required">*</span></label>
                    <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example" name="event_type">
                        <option selected name="category">Select Event Type</option>
                        @foreach($eventTicket as $eventTicket1)
                        <option value="{{$eventTicket1->category_id}}">{{ $eventTicket1->category->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button id="cancel-btn">Cancel</button>

                    <button type="submit" class="btn btn-primary">Submit</button>

                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const counter = document.getElementById("counter");
    const incrementBtn = document.getElementById("increment-btn");
    const decrementBtn = document.getElementById("decrement-btn");

    incrementBtn.addEventListener("click", function() {
        counter.value = parseInt(counter.value) + 1;
    });

    decrementBtn.addEventListener("click", function() {
        counter.value = parseInt(counter.value) - 1;
    });


    document.getElementById("cancel-btn").addEventListener("click", function() {
        window.history.back();
    });
</script>
@endsection('UserContent')