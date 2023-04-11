<form action="{{ route('razorpay') }}" method="get">
    <div>
        <input type="hidden" name="event_id" value="{{ $id }}">
        <input type="hidden" name="category_id" value="{{ $category }}">
       
        <input type="hidden" name="total_users" value="{{$total_users}}">
        <p> Price of Ticket ({{ $total_users }} Tickets) : {{ $total_price }}</p>
        <p>
            Booking Fee : {{ $fee }}
        </p>
        <p>
            Total Amount : {{ $amount }}
        </p>
        
    </div>



    <button type="submit"> Proceed to Pay</button>
</form>