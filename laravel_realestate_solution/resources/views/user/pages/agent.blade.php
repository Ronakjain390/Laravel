@extends('user/layouts/parts/master')
@section('content')
<div>
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
<div class="container">
    <div class="heading">
        <h1 class="heading-title">Search for Agent</h1>
    </div>
    <form method="GET" action="{{ route('searchagents') }}">
        <div class="row inputSearch">
            <div class="col-md-5 col-sm-8">
                <input type="text" name="agent_query" id="address" class="form-control search-input" placeholder="Search by name...">
                <button type="submit" class="btn btn-success search-btn">Search</button>
            </div>
        </div>
    </form>
    <div class="spacer agents">
        @foreach($data as $data)
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-lg-8 col-sm-8 agent">
                        <a href="#">
                            @if($data->image && File::exists(public_path('assets/images/agents/'. $data->image)) )
                            <img src="{{ asset('assets/images/agents/'. $data->image) }}" class="img-responsive" alt="agent name">
                            @else
                            <img src="{{ asset('assets/images/agents/default_agent.png') }}" class="img-responsive" alt="properties" />
                            @endif
                        </a>
                        <div class="agent_details">
                            <h4 class="agent_search_name">{{ $data->name }}</h4>
                            <p><span class="glyphicon glyphicon-map-marker"></span>
                                {{ $data->address }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-sm-4 agent_contact"><span class="glyphicon glyphicon-envelope"></span> <a href="#">{{ $data->email }}</a> <br>
                        <span class="glyphicon glyphicon-earphone"></span> {{ $data->contact }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .agent_search_name {
        margin-left: 9px !important;
        margin-bottom: 10px !important;
    }
</style>

@endsection('content')