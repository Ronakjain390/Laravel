@extends('user/layouts/parts/master')
@section('content')
<!-- banner -->
<div>
  @include('user/layouts/bannerSlider/bannerSlider')
</div>
<!-- banner -->

<br><br>
<div class="container">
  <div class="row property-container">
    @foreach($newhomes as $item)
    <div class="col-md-3 col-sm-4 col-xs-6 property-card">
      <div class="card" style="width: 100%;">
        @if($item->image && File::exists(public_path('assets/images/propertytype/'.$item->image)))
        <img src="{{ asset('assets/images/propertytype/'.$item->image) }}" class="property-Type" alt="Image" />
        @else
        <img src="{{ asset('assets/images/property/defaultPropertyDetail.jpg') }}" class="property-Type" alt="properties" />
        @endif
        <div class="card-body">
          <form method="get" action="{{ route('propertytypelisting') }}">
            <input type="text" name="ptype" id="ptype" value="{{ $item->id }}" hidden />
            <button type="submit" class="btn btn-primary">
              {{ $item->name }}
            </button>
          </form>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
<br>
<br>

<style>
  .property-container{
    padding-top: 50px;
  }
  .pagig {
    float: right;
  }

  .property-Type {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 20px;
  }
  .property-card{
    margin-bottom: 50px;
  }
  .card-body {
    margin-top: 10px;
    width: 100%;
  }
</style>
@endsection('content')