@extends('user/layouts/profileLayout/baseProfile')
@section('baseprofile')
<h1 class="heading-title">Edit Property</h1>
<div class="container-fluid">
    <div class="content-wrapper">
        <div class="inside-banner">
            <div class="property-container">
                <span class="pull-right"><a href="/">Home</a> / Add Property</span>
                <h2>Add Property</h2>
            </div>
        </div><br>
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data" action="{{ route('userPropertyUpdate', $property->id) }} " name="formPrevent">
                @csrf
                <!-- model value  -->
                <div id="myModel" class="modal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Agent Details</h5>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <label for="agent">Agents</label><br>
                                    <select class="form-control " name="agent" id="agent">
                                        @foreach ($agents as $agent)
                                        @if($agent->id == $property->agentid)
                                        <option selected value="{{ $property->agentid }}">{{ $agent->email }}</option>
                                        @else
                                        <option value="{{$agent->id}}">{{$agent->email}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="deleteButton" onclick="closeModel()" class="btn btn-close btn-danger">Close</button>
                                <button type="button" id="savebutton" onclick="closeModel()" class="btn btn-secondary">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- model value  -->
                <div class="row mt-2">
                    <div class="col-md-3 form-group">
                        <label for="name">Name </label>
                        <input type="text" class="form-control" name="name" id="name" value="{{$property->name }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="email">Email </label>
                        <input type="email" class="form-control" name="email" id="email" value="{{$property->email }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="contactNumber">Contact Number </label>
                        <input type="text" class="form-control" name="contactNumber" id="contactNumber" value="{{$property->contactNumber}}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="title">Title </label>
                        <input type="text" class="form-control" name="title" id="title" value="{{$property->title }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="reason">Sell or Rent </label>
                        <select class="form-control" name="reason" id="reason" required="">
                            <option selected>{{$property->reason }}</option>
                            <option value="sell">Sell</option>
                            <option value="rent">Rent</option>
                        </select>

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="priceType">Price Type </label>
                        <select class="form-control" name="priceType" id="priceType" required="">
                            <option selected>{{$property->priceType }}</option>
                            <option value="per square feet">Per square feet</option>
                            <option value="total">Total</option>
                        </select>

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="price">Price </label>
                        <input type="text" class="form-control" name="totalPrice" id="price" value="{{$property->totalPrice }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="builtArea">Builtup Area </label>
                        <input type="text" class="form-control" name="builtArea" id="builtArea" value="{{$property->builtArea }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="carpetArea">Carpet Area </label>
                        <input type="text" class="form-control" name="carpetArea" id="carpetArea" value="{{$property->carpetArea }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="bedroom">Bedroom </label>
                        <input type="text" class="form-control " name="bedroom" id="bedroom" value="{{$property->bedroom }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="bathroom">Bathroom </label>
                        <input type="text" class="form-control" name="bathroom" id="bathroom" value="{{$property->bathroom }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="address">Address </label>
                        <input type="text" class="form-control" name="address" id="address" value="{{$property->address }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="city">City </label>
                        <input type="text" class="form-control" name="city" id="city" value="{{$property->city }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="state">State </label>
                        <input type="text" class="form-control" name="state" id="state" value="{{$property->state }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="country">Country </label>
                        <input type="text" class="form-control" name="country" id="country" value="{{$property->country }}" />

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="postal_code">Postal Code </label>
                        <input type="text" class="form-control" name="postal_code" id="postal_code" value="{{$property->postal_code }}" />
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="reason">Property Type </label>
                        <select class="form-control" name="ptype" id="ptype" required="">
                            @foreach($propertytype as $ptype)
                            @if($ptype->id == $property->ptype)
                            <option selected value="{{$property->ptype}}">{{$ptype->name }}</option>
                            @else
                            <option value="{{$ptype->id}}">{{$ptype->name}}</option>
                            @endif
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="reason">Proeprty Flag</label>
                        <select class="form-control" name="propertyflag" id="propertyflag" required="">
                            @foreach($flags as $flag)
                            @if($flag->id == $property->propertyflag)
                            <option selected value="{{$property->propertyflag}}">{{$flag->flag_name }}</option>
                            @else
                            <option value="{{$flag->id}}">{{$flag->flag_name}}</option>
                            @endif
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-3 form-group">
                        <label for="reason">Proeprty Category</label>
                        <select class="form-control" name="propertycategory" id="propertycategory" required="">
                            @foreach($category as $cat)
                            @if($cat->id == $property->propertycategory)
                            <option selected value="{{$property->propertycategory}}">{{$cat->category_name}}</option>
                            @else
                            <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                            @endif
                            @endforeach
                        </select>

                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label><br />
                        <input type="checkbox" id="status" name="status" class="toggle-class" checked>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="amenities">General Amenities</label><br>
                        <select class="form-control " name="amenities" id="amenities" value="{{$property->amenities }}" multiple>
                            @foreach ($amenities as $amenities)
                            <option value="{{ $amenities->id }}">{{ $amenities->amenities }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="image">Image's of Property </label>
                        <input type="hidden" name="hidden_Image" value="{{ $property->image }}" />
                        <input type="file" class="form-control" id="image" name="image" />
                        <img src="{{ asset('assets/images/property/' . $property->image) }}" width="100" class="img-thumbnail" />
                    </div>
                    <div class="form-group col-md-4">
                        <label for="btn"><b>Do you want to assign this property to Agent ?</b></label><br />
                        <a class="btn btn-danger" onclick="togglemodel()">Agent</a>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-md-12 form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="content" class="form-control">{{$property->description }}</textarea>
                    </div>
                    <input type="hidden" class="form-control" name="longitude" id="longitude" hidden />
                    <input type="hidden" class="form-control" name="latitude" id="latitude" hidden />
                    <br>
                    <div class="col-md-12 ">
                        <button type="submit" class="btn btn-success read-more-btn btn-lg float-right px-5">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<br>
<script type="text/javascript">
    function togglemodel() {
        let mod = document.getElementById('myModel').style.display = "block"
    }

    function closeModel() {
        let clos = document.getElementById('myModel').style.display = "none"
    }
</script>

@endsection('baseprofile')