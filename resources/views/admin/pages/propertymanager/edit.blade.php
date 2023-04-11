@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-domain"></i>
                </span>Edit Property
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('propertymanager.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="POST" action="{{ route('propertymanager.update',$property->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-3 form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $property->name }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="{{ $property->email }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="contactNumber" class="form-control" name="contactNumber" id="contactNumber" value="{{ $property->contactNumber }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title" value="{{ $property->title }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="Utype">User Type</label>
                <select class="form-control" name="Utype" id="Utype">
                    <option selected>{{ $property->Utype }}</option>
                    <option value="owner">Owner</option>
                    <option value="agent">Agent</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="reason">Sell or Rent:</label>
                <select class="form-control" name="reason" id="reason">
                    <option selected>{{ $property->reason }}</option>
                    <option value="sell">Sell</option>
                    <option value="rent">Rent</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="priceType">Price Type:</label>
                <select class="form-control" name="priceType" id="priceType">
                    <option selected>{{ $property->priceType }}</option>
                    <option value="per square feet">Per square feet</option>
                    <option value="total">Total</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="Utype">Property Type</label>
                <select class="form-control" name="ptype" id="ptype">
                    @foreach($propertyType as $protype)
                    @if($property->ptype == $protype->id)
                    <option selected value="{{$property->propertyflag}}">{{ $protype->name }}</option>
                    @else
                    <option value="{{$protype->id}}">{{$protype->name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="Utype">Assign User</label>
                <select class="form-control" name="ptype" id="ptype">
                    @foreach($user as $user)
                    @if($user->id == $property->userId)
                    <option selected value="{{$property->userid}}">{{ $user->name }}</option>
                    @else
                    <option value="{{$user->id}}">{{$user->name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="Utype">Assign Agents</label>
                <select class="form-control" name="ptype" id="ptype">
                    @foreach($agent as $agent)
                    @if($agent->id == $property->agentid)
                    <option selected value="{{$property->agentid}}">{{ $agent->name }}</option>
                    @else
                    <option value="{{$agent->id}}">{{$agent->name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="Utype">Property Category</label>
                <select class="form-control" name="procategory" id="procategory">
                    @foreach($category as $procategory)
                    @if($property->propertycategory == $procategory->id )
                    <option selected value="{{$property->propertycategory}}">{{ $procategory->category_name }}</option>
                    @else
                    <option value="{{ $procategory->id }}">{{ $procategory->category_name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="Utype">Flags</label>
                <select class="form-control" name="proflags" id="proflags">
                    @foreach($flags as $propertyflag)
                    @if($property->propertyflag == $propertyflag->id)
                    <option selected value="{{$property->propertycategory}}">{{ $propertyflag->flag_name }}</option>
                    @else
                    <option value="{{ $propertyflag->id }}">{{ $propertyflag->flag_name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" name="totalPrice" id="price" value="{{ $property->totalPrice }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="builtArea">Builtup Area</label>
                <input type="text" class="form-control" name="builtArea" id="builtArea" value="{{ $property->builtArea }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="carpetArea">Carpet Area</label>
                <input type="text" class="form-control" name="carpetArea" id="carpetArea" value="{{ $property->carpetArea }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="bedroom">Number of Bedroom</label>
                <input type="text" class="form-control" name="bedroom" id="bedroom" value="{{ $property->bedroom }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="bathroom">Number of Bathroom</label>
                <input type="text" class="form-control" name="bathroom" id="bathroom" value="{{ $property->bathroom }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ $property->address }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" name="city" id="city" value="{{ $property->city }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="state">State</label>
                <input type="text" class="form-control" name="state" id="state" value="{{ $property->state }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="country">Country</label>
                <input type="text" class="form-control" name="country" id="country" value="{{ $property->country }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" class="form-control" name="postal_code" id="postal_code" value="{{ $property->postal_code }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="amenities">General Amenities</label>
                <select class="selectpicker form-control @error('amenities') is-invalid @enderror" name="amenities" id="amenities" multiple>
                    @foreach ($amenities as $generalAmenities)
                    @if($property->generalAmenities == $generalAmenities->id)
                    <option selected value="{{$property->generalAmenities}}">{{ $generalAmenities->amenities }}</option>
                    @else
                    <option value="{{ $generalAmenities->id }}">{{ $generalAmenities->amenities }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="image">Image's of Property</label>
                <input type="file" class="form-control" name="image" id="image" multiple />
                <input type="hidden" name="hidden_Image" value="{{ $property->image }}" />
            </div>
            <div class="form-group col-md-4">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" class="toggle-class" {{ $property->status=='1' ? 'checked':'' }}>
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" id="content" class="form-control">{{ $property->description }}</textarea>
            </div>
            
            <input type="text" class="form-control" name="longitude" id="longitude" hidden />
            <input type="text" class="form-control" name="latitude" id="latitude" hidden />
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')