@extends('user/layouts/profileLayout/baseProfile')
@section('baseprofile')
<!-- banner -->
<!-- <div class="">
    @include('user/layouts/bannerSlider/bannerSlider')
</div> -->
@if (Session::has('success'))

<div class="alert alert-success">
  <p>{{ Session::get('success') }}</p>
</div>
@endif
<h1 class="heading-title">Listing Properties</h1>
<div class="property-container">
  <div class="spacer blog">
    <div class="row">
      <div class="col-sm-12 ">
        @if($properties == '')
        <div class="row">
          There is no Data.
        </div>
        @else
        @foreach($properties as $item)
        <!-- blog list -->
        <div class="row">
          <div class="col-lg-4 col-sm-4 "><a href="{{ route('userPropertyShow', ['id' => $item->id]) }}" class="thumbnail">
              @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)))
              <img src="{{ asset('assets/images/property/'.$item->image) }}" alt="img">
              @else
              <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" alt="properties" />
              @endif
            </a></div>
          <div class="col-lg-5 col-sm-5 ">
            <h3><a href="{{ route('userPropertyShow', ['id' => $item->id]) }}">{{ $item->title }}</a></h3>
            <div class="info">Posted on: {{ $item->created_at->format(config('app.dateFormate')) }}</div>
            @if($item->description)
            <p>{!! substr($item->description, 0, 150) !!} </p>
            @endif
          </div>
          <div class="col-lg-3 col-sm-3 ">
            <a href="{{ route('userPropertyEdit',['id' => $item->id]) }}" class="btn btn-success btnEdit">
              <i class='fa fa-edit'></i>
            </a>
            <a href="{{ route('userPropertyDelete',['id' => $item->id]) }}" class="btn btn-success btnDelete">
              <i class='fa fa-trash'></i>
            </a>
            <div>
              <form method="POST" action="{{ route('userPropertyStatus', $item->id) }}">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success {{ $item->status == 1 ? 'statusActive' : 'statusInactive' }}" name="action" value="status_toggle">
                  <i class="fa {{ $item->status == 1 ? 'fa-check' : 'fa-ban' }}"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
        <!-- blog list -->
        @endforeach
        @endif
      </div>
    </div>
  </div>
</div>
<style>
  .btnDelete {
    background-color: #e4002b;
  }

  .statusInactive {
    background-color: #e4002b;
  }

  .statusActive {
    background-color: #00639E;
  }
</style>
@endsection('baseprofile')