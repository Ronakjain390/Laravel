@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">General Settings /</span> 
        @if(isset($generalsetting->id)==1)
        <a href="{{ route('EditGeneralSetting', $generalsetting->id) }}" class="btn rounded-pill btn-primary float-right">Edit</a></h4>
        @else
        <a href="{{ route('EditGeneralSetting', isset($generalsetting->id)) }}" class="btn rounded-pill btn-primary float-right">Edit</a></h4>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">

                    <!-- Account -->

                    <hr class="my-0" />
                    <div class="card-body">
                        <div class="row" style="margin:0,20px">
                            <div class="form-group col-md-4">
                                <label class="col-sm-2 col-label-form"><b>Logo</b></label>
                                <div class="col-sm-10 imagestyle">
                                    @if(isset($generalsetting->logo)==1)
                                    <img src="{{ asset('Assets/images/' . $generalsetting->logo) }}" width="200" class="img-thumbnail" />
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class=""><b>Favicon</b></label>
                                <div class="col-sm-10 imagestyle">
                                    @if(isset($generalsetting->favicon)==1)
                                    <img src="{{ asset('Assets/images/' . $generalsetting->favicon)}}" width="200" class="img-thumbnail" />
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class=""><b>Logo mini</b></label>
                                <div class="col-sm-10 imagestyle">
                                    @if(isset($generalsetting->logo_mini)==1)
                                    <img src="{{ asset('Assets/images/' . $generalsetting->logo_mini )}}" width="200" class="img-thumbnail" />
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Title</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->title) ? $generalsetting->title : '' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Copyright Text</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->copyright_text) ? $generalsetting->copyright_text: ''  }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Email</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->email) ? $generalsetting->email : ''  }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Contact Number</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->contact_number) ? $generalsetting->contact_number: ''  }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Address</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->address) ? $generalsetting->address : ''  }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>City</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->city) ? $generalsetting->city : '' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>State</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->state) ? $generalsetting->state : ''  }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Country</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->country) ? $generalsetting->country :''  }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Postal Code</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->zipCode) ? $generalsetting->zipCode : ''  }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-label-form"><b>Date</b></label>
                            <div class="col-sm-10">
                                {{ isset($generalsetting->date_format) ? $generalsetting->date_format : '' }}
                            </div>
                        </div>
                    </div>
                    <!-- /Account -->
                </div>

            </div>
        </div>
    </div>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>

<style>
    .imagestyle {
        margin-top: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection('SuperAdminContent')