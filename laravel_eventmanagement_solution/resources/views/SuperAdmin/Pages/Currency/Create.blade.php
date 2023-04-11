@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Currency</h5>
                    <a href="{{ route('Currency') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                @if (Session::has('success'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('StoreCurrency') }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="countryname">Country Name<span class="required">*</span></label>
                                <input type="text" class="form-control" name="country_name" id="countryname" placeholder="Country Name" />
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="countrycode">Country Code<span class="required">*</span></label>
                                <input type="text" class="form-control" name="country_code" id="countrycode" placeholder="Country Code" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="currencysymbol">Currency Symbol<span class="required">*</span></label>
                            <input type="text" class="form-control" name="currency_symbol" id="currencysymbol" placeholder="Currency Symbol" />
                        </div>
                        <div class="mb-3">
                            <label for="value">Value</label>
                            <input type="text" class="form-control" placeholder="Enter Currency Value" id="value" name="value" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="default">Default Value<span class="required">*</span></label>
                            <input type="checkbox" name="default" id="default" placeholder="Default Value" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-postal_code">Status</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" checked />
                                <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')