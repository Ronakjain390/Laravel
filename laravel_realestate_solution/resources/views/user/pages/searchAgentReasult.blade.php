@extends('user/layouts/parts/master')
@section('content')
<div class="inside-banner">
    <div class="container bread">
        <span class="pull-left"><a href="/">Home</a> / {{ app('request')->input('agent_query') }}</span>
    </div>
</div>
<div class="container">
    <br><br>
    <form method="GET" action="{{ route('searchagents') }}">
        <div class="row inputSearch">
            <div class="col-md-5">
                <input type="text" name="agent_query" id="address" class="form-control" placeholder="Search by name..." value="{{app('request')->input('agent_query')}}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success">Search</button>
            </div>
        </div>
    </form>

    <div class="spacer agents">
        @foreach($data as $data)
        <div class="row">
            <div class="col-lg-8  col-lg-offset-2 col-sm-12">
                <div class="row">
                    <div class="col-lg-2 col-sm-2 ">
                        <a href="#">
                            @if($data->image && File::exists(public_path('assets/images/agents/'. $data->image)) )
                            <img src="{{ asset('assets/images/agents/'. $data->image) }}" class="img-responsive" alt="agent name">
                            @else
                            <img src="{{ asset('assets/images/agents/default_agent.png') }}" class="img-responsive" alt="properties" />
                            @endif
                        </a>
                    </div>
                    <div class="col-lg-5 col-sm-5 ">
                        <h4 class="agent_search_name">{{ $data->name }}</h4>
                        <p><span class="glyphicon glyphicon-map-marker"></span>
                            {{ $data->address }}
                        </p>
                    </div>
                    <div class="col-lg-5 col-sm-5 "><span class="glyphicon glyphicon-envelope"></span> <a href="#">{{ $data->email }}</a><br>
                        <span class="glyphicon glyphicon-earphone"></span> {{ $data->contact }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<style>
    .container.bread {
        height: 45px;
    }

    .agent_search_name {
        margin-left: 9px !important;
        margin-bottom: 10px !important;
    }
</style>
@endsection('content')