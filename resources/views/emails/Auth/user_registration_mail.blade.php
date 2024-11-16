@component('mail::message')
#'Hello, Admin New user has been registered. 

User Id : {{ $userData->id }} <br>
Name : {{ $userData->name }} <br>
Email : {{ $userData->email}} <br>
Phone Number : {{ $userData->phone}} <br>

Thanks, <br>
TheParchi

<div class="footer">
    <!-- Footer content goes here -->
     <br>
     <a href="https://theparchi.com/" style="color: black;"><img src="{{asset('image/Vector.png')}}" alt="theparchi"></a>
     <br>

    <small>POWERED BY</small> <a href="https://theparchi.com/" style="color: black; margin-top:5px;">www.TheParchi.com</a>
</div>
@endcomponent
