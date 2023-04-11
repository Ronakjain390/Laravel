@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-more"></i>
                </span> Edit City
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('cities.index') }}" class="btn btn-success btn-lg float-right">Back</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    @if(session('status'))
    <div class="alert alert-success mb-1 mt-1">
        {{ session('status') }}
    </div>
    @endif
    <form method="POST" action="{{ route('cities.update',$city->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-4 form-group">
                <label for="countryname">City Name </label>
                <input type="text" name="city" class="form-control" value="{{ $city->city }}">
            </div>
            <div class="col-md-3 form-group">
                <label for="reason">Country<span class="required">*</span></label>

                <select class="form-control @error('country') is-invalid @enderror" name="countryid" id="countryid">
                    @foreach($country as $item)
                    @if($city->countryid == $item->id)
                    <option selected value="{{ $city->countryid }}">{{$item->countryname}}</option>
                    @else 
                    <option value="{{$item->id}}">{{$item->countryname}}</option>
                    @endif
                    @endforeach
                </select>
                @error('reason')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="reason">state<span class="required">*</span></label>
                <select class="form-control @error('country') is-invalid @enderror" name="stateid" id="stateid">
                    @foreach($state as $item)
                    @if($city->stateid == $item->id)
                    <option selected value="{{ $city->stateid }}">{{$item->statename}}</option>
                    @else 
                    <option value="{{$item->id}}">{{$item->statename}}</option>
                    @endif
                    @endforeach
                </select>
                @error('reason')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-2">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" {{ $city->status=='1' ? 'checked':'' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')