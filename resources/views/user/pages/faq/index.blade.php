@extends('user/layouts/parts/master')
@section('content')

<div class="container-fluid">
<div class="">
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
    <div class="container">
        <div class="heading">
            <h1 class="heading-title">FAQ's</h1>
        </div>
        <!-- <div class="accordion accordion-flush" id="accordionFlushExample">
            @foreach($data as $index=>$data1)
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    <b> {{ $index+1 }}. {{$data1->question}}</b>
                </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body"><p>{!! $data1->answer !!}</p></div>
                </div>
            </div>
            @endforeach
        </div> -->
        @foreach($data as $index=>$data1)
        <button class="accordion"><b> {{ $index+1 }}. {{$data1->question}}</b></button>
        <div class="panel">
            <p>{!! $data1->answer !!}</p>
        </div>
        <br><br>
        @endforeach
    </div>
</div>
<br> <br><br>

<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            /* Toggle between adding and removing the "active" class,
            to highlight the button that controls the panel */
            this.classList.toggle("active");

            /* Toggle between hiding and showing the active panel */
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
</script>


@endsection('content')