@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">General Settings /</span> Account</h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">General Details</h5>
                    <!-- Account -->
                    <hr class="my-0" />
                    <div class="card-body">
                        @if(isset($generalsetting->id)==1)
                        <form action="{{ route('UpdateGeneralSetting',$generalsetting->id) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @else
                        <form action="{{ route('UpdateGeneralSetting',isset($generalsetting->id)) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @endif

                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo" />
                                    @if(isset($generalsetting->logo )==1)
                                    <input type="hidden" name="hidden_logo" value="{{ $generalsetting->logo }}" />
                                    <img src="{{ asset('Assets/images/' . $generalsetting->logo) }}" width="200" class="img-thumbnail" />
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="logo_mini">Mini Logo</label>
                                    <input type="file" class="form-control" id="logo_mini" name="logo_mini" />
                                    @if(isset($generalsetting->logo_mini )==1)
                                    <input type="hidden" name="hidden_logo_mini" value="{{ $generalsetting->logo_mini }}" />
                                    <img src="{{ asset('Assets/images/' .$generalsetting->logo_mini) }}" width="200" class="img-thumbnail" />
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="favicon">Favicon</label>
                                    <input type="file" class="form-control" id="favicon" name="favicon" />
                                    @if(isset($generalsetting->favicon )==1)
                                    <input type="hidden" name="hidden_favicon" value="{{ $generalsetting->favicon }}" />
                                    <img src="{{ asset('Assets/images/' . $generalsetting->favicon) }}" width="200" class="img-thumbnail" />
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="copyrighttext" class="form-label">Copyright Text</label>
                                    @if(isset($generalsetting->copyright_text )==1)
                                    <input class="form-control" type="text" id="copyrighttext" name="copyright_text" placeholder="Copyright Text" autofocus value="{{ $generalsetting->copyright_text }}" />
                                    @else
                                    <input class="form-control" type="text" id="copyrighttext" name="copyright_text" placeholder="Copyright Text" autofocus  />
                                    @endif
                                    
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="title" class="form-label">Title</label>
                                    @if(isset($generalsetting->title )==1)
                                    <input class="form-control" type="text" id="title" name="title" placeholder="Title" autofocus value="{{ $generalsetting->title }}" />
                                    @else
                                    <input class="form-control" type="text" id="title" name="title" placeholder="Title" autofocus />
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    @if(isset($generalsetting->email )==1)
                                    <input class="form-control" type="email" name="email" id="email" placeholder="Email" value="{{ $generalsetting->email }}" />
                                    @else
                                    <input class="form-control" type="email" name="email" id="email" placeholder="Email"  />
                                    @endif
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    @if(isset($generalsetting->contact_number )==1)
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="098 765 4321" value="{{ $generalsetting->contact_number }}" />
                                    @else
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="098 765 4321" />
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="country">Country</label>
                                    <select id="country" name="country" class="select2 form-select">
                                        @if(isset($generalsetting->country)==1))
                                        <option selected value="{{$generalsetting->country}}">{{$generalsetting->country}}</option>
                                        @endif
                                        <option value="Australia">Australia</option>
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="Belarus">Belarus</option>
                                        <option value="Brazil">Brazil</option>
                                        <option value="Canada">Canada</option>
                                        <option value="China">China</option>
                                        <option value="France">France</option>
                                        <option value="Germany">Germany</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Israel">Israel</option>
                                        <option value="Italy">Italy</option>
                                        <option value="Japan">Japan</option>
                                        <option value="Korea">Korea, Republic of</option>
                                        <option value="United Arab Emirates">United Arab Emirates</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="United States">United States</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="state" class="form-label">State</label>
                                    @if(isset($generalsetting->state)==1)
                                    <input class="form-control" type="text" id="state" name="state" placeholder="California" value="{{ $generalsetting->state }}" />
                                    @else
                                    <input class="form-control" type="text" id="state" name="state" placeholder="California"  />
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    @if(isset($generalsetting->city)==1)
                                    <input class="form-control" type="text" id="city" name="city" placeholder="City" value="{{ $generalsetting->city }}" />
                                    @else
                                    <input class="form-control" type="text" id="city" name="city" placeholder="City"/>
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="zipCode" class="form-label">Zip Code</label>
                                    @if(isset($generalsetting->zipCode)==1)
                                    <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="231465" maxlength="6" value="{{ $generalsetting->zipCode }}" />
                                    @else
                                    <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="231465" maxlength="6" />
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="address" class="form-label">Address</label>
                                    @if(isset($generalsetting->address)==1)
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{ $generalsetting->address }}" />
                                    @else
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" />
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="address" class="form-label">Date Format</label>
                                    @if(isset($generalsetting->date_format)==1)
                                    <input type="text" class="form-control" id="date_format" name="date_format" placeholder="Date Format" value="{{ $generalsetting->date_format }}" />
                                    @else
                                    <input type="text" class="form-control" id="date_format" name="date_format" placeholder="Date Format" />
                                    @endif
                                </div>
                                <input type="text" class="form-control" name="longitude" id="longitude" hidden />
                                <input type="text" class="form-control" name="latitude" id="latitude" hidden />
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                <a href="{{ route('GeneralSetting') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                        
                    </div>
                    <!-- /Account -->
                </div>

            </div>
        </div>
    </div>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
@endsection('SuperAdminContent')