@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<?php
$currency = currency();
$currency_symbol = $currency[0]->currency_symbol;
?>
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="category-browse--header">
    <div class="category-browse--header-">
        <div class="container">
            <div class="category-browse--header-text">
                <div class="category-browse--header-text__wrapper">
                    <h1 class="category-browse__header--content">Order History <div class="eds-text-bl" style="color:#FFF58C;padding-top:8px"></div>
                    </h1>
                    <p></p>
                </div>
            </div>
        </div>
        <!-- <aside class="category-browse--header-image category-browse--header-image--square"><img fetchpriority="high" class="full-width-img" loading="eager" src="" alt="[object Object]"></aside> -->
    </div>
</div>
<div class="container">
    <div class="section-top-border">


        <div class="progress-table-wrap">
            <div class="progress-table">
                <div class="table-head">
                    <div class="serial">S. No.</div>
                    <div class="country">Event Name</div>
                    <div class="visit">Start Date</div>
                    <div class="visit">End Date</div>
                    <!-- <div class="visit">Status</div> -->
                    <div class="visit">Action</div>
                </div>
                @foreach($tickets->keys() as $index=>$depart)
                
               
                @foreach($eventDetail as $department1)
                @if($depart == $department1->id)
                <div class="table-row">
                    <div class="serial">{{ $index+1 }} </div>
                    <div class="country"> {{ Str::limit($department1->title, 15, $end = '...') }}</div>
                    <div class="visit">{{date('D, d M Y', strtotime($department1->start_date))}}</div>
                    <div class="visit">{{date('D, d M Y', strtotime($department1->end_date))}}</div>

                    <!-- <div class="visit">
                        <div class="genric-btn success circle small">
                            @if($department1->status == 1)
                            Active
                            @else
                            Inactive
                            @endif
                          
                        </div>
                    </div> -->
                    
                    <div class="visit"> <a method="get" href="{{ route('TicketListingDetail', [ 'id' => $department1->id ]) }}"><i class="fa fa-eye" aria-hidden="true" style="font-size: 24px;"></i> </a>
                    </div>
                </div>
                @endif
                @endforeach
                @endforeach

            </div>
        </div>
    </div>
</div>

<style>
    .table-header {
        background-color: #95A5A6;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .table-row {
        background-color: #ffffff;
        box-shadow: 0px 0px 9px 0px rgba(0, 0, 0, 0.1);
    }

    .col-1 {
        flex-basis: 10%;
    }

    .col-2 {
        flex-basis: 40%;
    }

    .col-3 {
        flex-basis: 25%;
    }

    .col-4 {
        flex-basis: 25%;
    }

    .col-5 {
        flex-basis: 25%;
    }

    .col-6 {
        flex-basis: 25%;
    }

    @media all and (max-width: 767px) {
        .table-header {
            display: none;
        }

        li {
            display: block;
        }

        .col {
            flex-basis: 100%;
        }

        .col {
            display: flex;
            padding: 10px 0;

        }

        .col:before {
            color: #6C7A89;
            padding-right: 10px;
            content: attr(data-label);
            flex-basis: 50%;
            text-align: right;
        }
    }
</style>
@endsection('UserContent')