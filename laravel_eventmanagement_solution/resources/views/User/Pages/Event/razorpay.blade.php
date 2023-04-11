

<script>
var options = {
    "key": "{{ env('RAZOR_KEY') }}", // Enter the Key ID generated from the Dashboard
    "amount": "{{$grandTotal}}00", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
    "currency": "INR",
    "name": "Addweb", //your business name
    "description": "Test Transaction",
    "callback_url": "{{ route('payment') }}",
    "prefill": {
        "name": "{{Auth::user()->name}}", //your customer's name
        "email": "{{ Auth::user()->email  }}",
        "contact": "{{Auth::user()->contact_number}}"
    },  
    "notes": {
        "address": "Razorpay Corporate Office",
        "eventId": "{{$eventId}}",
        "fee": "{{ $fee }}",
        "catIds": "{{ $catIds }}",
        "catTickets": "{{ $catTickets }}",
    },
    "theme": {
        "color": "#3399cc"
    }
};
var rzp1 = new Razorpay(options);
document.getElementById('rzp-button1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>