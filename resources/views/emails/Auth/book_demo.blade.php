@component('mail::message')
<style>
     .footer {
            position: fixed;
            width: 100%;
            text-align: center;
            color: black;
            line-height: 8px;
            bottom: 0;
        }


        .footer img {
            height: 35px;
            margin: 8px;
            margin-bottom: 10px;
        }
</style>
#'Hello, Admin New Demo Has Been Booked. 

Name : {{ $userData->name }} <br>
Email : {{ $userData->email}} <br>
Phone Number : {{ $userData->phone}} <br>

Thanks, <br>
TheParchi

<div class="footer">
    <!-- Footer content goes here -->
     <br>
     <a href="https://theparchi.com/" style="color: black;"><img src="{{asset('image/Vector.png')}}" alt="theparchi"></a>
   
</div>
@endcomponent
