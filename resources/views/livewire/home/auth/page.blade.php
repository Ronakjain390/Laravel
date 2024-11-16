<div class="card max-w-7xl h-full bg-[#E7E7E7] mx-auto  rounded text-black">
    <div class="navbar bg-white">
        <div class="navbar-start">
          <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <a href="{{ route('dashboard') }}"
                class="flex items-center justify-center  sm:ml-auto my-3  border-gray-400 ">
                <img src="{{asset('image/Vector.png')}}" class="h-8 mr-3" alt="TheParrchi" />
                <!-- <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">TheParchi</span> -->
             </a>
            </div>
             
          </div>
        </div>
        <div class="navbar-center">
          <a class="btn btn-ghost text-xl" href="{{ route('dashboard') }}">WELCOME TO THEPARCHI</a>
        </div>
        <div class="navbar-end">
           
            
        </div>
      </div>
    <div class="card-body items-center text-center">
        <div class="text-justify text-base font-normal">
            {{-- @dump($receiverData['page']); --}}
            @if ($receiverData && isset($receiverData['page']))
            <h1 class="text-center text-2xl p-3">{{ $receiverData['page']->title }}</h1>
            <p>{{ $receiverData['page']->content }}</p>

            {{-- <p>Created at: {{ $receiverData['page']['created_at'] }}</p>
            <p>Updated at: {{ $receiverData['page']['updated_at'] }}</p> --}}
            @else
            <p>No page data available.</p>
            @endif

        </div>
    </div>
    <div class="col-span-12 mt-4 mb-4">
        <div class="col-12 text-center mt-4">
            <a href="{{ route('page', ['slug' => 'privacy-policy']) }}" class="text-blue-400 underline">Privacy Policy |</a>
            <a href="{{ route('page', ['slug' => 'terms-and-conditions']) }}"
                class="text-blue-400 underline a-active">Terms & Conditions |</a>
            <a href="{{ route('page', ['slug' => 'cancellation-policy']) }}"
                class="text-blue-400 underline">Cancellation Policy</a>
        </div>
    </div>
</div>