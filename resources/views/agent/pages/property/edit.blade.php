@extends('agent/layouts/master')

@section('content')
<div class="container-fluid my-5">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title text-center">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-domain"></i>
                </span>Edit Property
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('property.index') }}" class="btn btn-outline-success btn-lg float-right">Back</a>
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
    <form class="form bg-white p-4 rounded shadow" method="POST" action="{{ route('property.update',$property->id) }}" enctype="multipart/form-data">
        @csrf
        <div id="myModel" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agent Details</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="user">User</label><br>
                            <select class="form-control" name="userId" id="userId">
                                @foreach($owner as $owner)
                                @if($owner->id == $property->userId)
                                <option selected value="{{ $property->userId }}">{{ $owner->email }}</option>
                                @else
                                <option value="{{$owner->id}}">{{$owner->email}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="deleteButton" onclick="closeModel()" class="btn btn-danger">Close</button>
                        <button type="button" id="savebutton" onclick="closeModel()" class="btn btn-secondary">Add</button>
                    </div>
                </div>
            </div>
        </div>
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
                <label for="priceType">Category <span class="required">*</span></label>
                <select class="form-control @error('category') is-invalid @enderror" name="procategory" id="procategory">
                    @foreach($category as $category)
                    @if($category->id == $property->propertycategory)
                    <option selected value="{{ $property->propertycategory }}">{{ $category->category_name }}</option>
                    @else
                    <option value="{{$category->id}}">{{$category->category_name}}</option>
                    @endif
                    @endforeach
                </select>
                @error('procategory')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-md-3 form-group">
                <label for="ptype">Property Type <span class="required">*</span></label>
                <select class="form-control @error('ptype') is-invalid @enderror" name="ptype" id="ptype">
                    @foreach($propertytype as $ptype)
                    @if($ptype->id == $property->ptype)
                    <option selected value="{{$property->ptype}}">{{$ptype->name}}</option>
                    @else
                    <option value="{{$ptype->id}}">{{ $ptype->name }}</option>
                    @endif
                    @endforeach
                </select>
                @error('ptype')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="priceType">Flags <span class="required">*</span></label>
                <select class="form-control @error('flags') is-invalid @enderror" name="flags" id="flags">
                    @foreach($flags as $flag)
                    @if($flag->id == $property->propertyflag)
                    <option value="{{ $property->propertyflag }}">{{ $flag->flag_name }}</option>
                    @else
                    <option value="{{ $flag->id }}">{{ $flag->flag_name }}</option>
                    @endif
                    @endforeach
                </select>
                @error('ptype')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="amenities">General Amenities</label>
                <select class="selectpicker form-control" name="generalAmenities" id="amenities" multiple>
                    @foreach ($amenities as $amenities)
                    <option value="{{ $amenities->id }}" selected>{{ $amenities->amenities }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="image">Image's of Property</label>
                <input type="file" class="form-control" name="image" id="image" multiple />
                <input type="hidden" name="hidden_Image" value="{{ $property->image }}" />
            </div>
            <div class="form-group col-md-3">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" class="toggle-class" {{ $property->status=='1' ? 'checked':'' }}>
            </div>
            <div class="col-md-6 form-group">
                <label for="description">Description</label>
                <textarea name="description" id="content" class="form-control" rows="10">{{ $property->description }}</textarea>
            </div>
            <div class="form-group col-md-3">
                <label for="btn"><b>Do you want to assign this property to Owner ?</b></label><br />
                <a class="btn btn-danger" onclick="togglemodel()">Owner</a>
            </div>
            <input type="text" class="form-control" name="longitude" id="longitude" hidden />
            <input type="text" class="form-control" name="latitude" id="latitude" hidden />
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right text-white px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    function togglemodel() {
        let mod = document.getElementById('myModel').style.display = "block"
    }

    function closeModel() {
        let clos = document.getElementById('myModel').style.display = "none"
    }
</script>
<style>
    .container-fluid {
        width: 90%;
    }
</style>
@endsection('content')