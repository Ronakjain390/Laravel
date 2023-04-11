@extends('user/layouts/profileLayout/baseProfile')
@section('baseprofile')
<h1 class="heading-title">View Property</h1>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="row mb-3">
                    <label class="col-label-form"><b>Name</b></label>
                    <div>
                        {{ $property->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-label-form"><b>Email</b></label>
                    <div>
                        {{ $property->email }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Conatct Number</b></label>
                    <div>
                        {{ $property->contactNumber }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Property Title</b></label>
                    <div>
                        {{ $property->title }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>User Type</b></label>
                    <div>
                        {{ $property->Utype }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Type</b></label>
                    <div>
                        {{ $property->reason }}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Type of Price</b></label>
                    <div>
                        {{ $property->priceType }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Total Price</b></label>
                    <div>
                        {{ $property->totalPrice }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>User Type</b></label>
                    <div>
                        {{ $property->Utype }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Agent Name</b></label>
                    <div>
                        {{ $agentName->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Type</b></label>
                    <div>
                        {{ $property->reason }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-label-form"><b>Bedroom</b></label>
                    <div>
                        {{ $property->bedroom }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-label-form"><b>Bathroom</b></label>
                    <div>
                        {{ $property->bathroom }}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Address</b></label>
                    <div>
                        {{ $property->address }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>City</b></label>
                    <div>
                        {{ $property->city }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>State</b></label>
                    <div>
                        {{ $property->state }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Country</b></label>
                    <div>
                        {{ $property->country }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-label-form"><b>Postal Code</b></label>
                    <div>
                        {{ $property->postal_code }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label><b>Property Category</b></label>
                <div>
                    @foreach($category as $category)
                    @if($category->id == $property->propertycategory)
                    {{ $category->category_name}}
                    @endif
                    @endforeach 
                </div>
            </div>
            <div class="col-md-4">
                <label><b>Flags</b></label>
                <div>
                    @foreach($flags as $flag)
                    @if($flag->id == $property->propertyflag)
                    {{ $flag->flag_name }}
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="col-md-4">
                <label><b>Property Type</b></label>
                <div>
                    @foreach($propertytype as $ptype)
                    @if($ptype->id == $property->ptype)
                    {{ $ptype->name }}
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label><b>General Amenities</b></label>
                <div>
                    {{ $amenities[0]->amenities }}
                </div>
            </div>
            <div class="col-md-4">
                <label><b>Description</b></label>
                <div>
                    {!! $property->description !!}
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-4">
                @if($property->image && File::exists(public_path('assets/images/property/'.$property->image)))
                <img src="{{ asset('assets/images/property/' . $property->image) }}" width="200" class="img-thumbnail" />
                @else
                <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" />
                @endif
            </div>
        </div>
    </div>
</div>
@endsection('baseprofile')