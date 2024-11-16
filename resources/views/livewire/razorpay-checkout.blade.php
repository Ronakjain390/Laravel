<div>

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<form action="/payment-initiate" method="POST">
@csrf
<script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="{{ env('RAZOR_KEY') }}"
    data-amount="{{ $amountWithGst * 100}}"
    data-currency="INR"
    data-order_id=""  
    data-name="The Parchi"
    data-description="The Parchi"
    data-image="/image/Vector.png"
    data-prefill.name="{{ auth()->user()->name }}"
    data-prefill.email="{{ auth()->user()->email }}"
    data-theme.color="#7f5be8"
></script>
<input type="hidden" custom="Hidden Element" name="hidden"/>
<input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
<input type="hidden" name="amount" value="{{ $amountWithGst}}">


</form>
{{-- <form action="/payment-initiate" method="POST"  >
@csrf



<button data-processorid="razor" class="js-pay-bundle bg-white border border-black p-1 rounded-lg hover:bg-orange" >Pay Now</button>

<!-- Script to dynamically load Razorpay checkout -->
<script>
    // Function to reload the Razorpay checkout script with new values
    function reloadRazorpayCheckout(amountWithGst) {
        // Remove existing script element
        var existingScript = document.querySelector('script[data-key="{{ env('RAZOR_KEY') }}"]');
        if (existingScript) {
            existingScript.remove();
        }

        // Create new script element with updated values
        var script = document.createElement('script');
        script.src = "https://checkout.razorpay.com/v1/checkout.js";
        script.setAttribute('data-key', "{{ env('RAZOR_KEY') }}");
        script.setAttribute('data-amount', (amountWithGst * 100).toFixed(0)); // Convert to the appropriate format
        script.setAttribute('data-currency', "INR");
        script.setAttribute('data-order_id', "");
        script.setAttribute('data-name', "The Parchi");
        script.setAttribute('data-description', "The Parchi");
        script.setAttribute('data-image', "/image/Vector.png");
        script.setAttribute('data-prefill.name', "{{ auth()->user()->name }}");
        script.setAttribute('data-prefill.email', "{{ auth()->user()->email }}");
        script.setAttribute('data-theme.color', "#7f5be8");

        // Append the new script element to the document body
        document.body.appendChild(script);
    }

    // Call the function to reload the script with the initial value
    reloadRazorpayCheckout({{ $amountWithGst }});

  
</script>

<input type="hidden" custom="Hidden Element" name="hidden">
<input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
<input type="hidden" name="amount" value="{{$amountWithGst}}">

@if(isset($planIds) && !empty($planIds))
@foreach($planIds as $planId)
    <input type="hidden" name="plan_ids[]" value="{{ $planId }}">
@endforeach
@endif

@if(isset($topupIds) && !empty($topupIds))
@foreach($topupIds as $topupId)
    <input type="hidden" name="feature_topup_ids[]" value="{{ $topupId }}">
@endforeach
@endif

</form> --}}
 
 
 
<style>
.razorpay-payment-button {
background-color: white;
color: black;
padding: 5px;
border-radius: 10%;
border: 1px solid black;
}

</style>
</div>