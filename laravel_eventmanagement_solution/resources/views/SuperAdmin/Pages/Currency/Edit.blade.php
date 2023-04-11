@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Currency</h5>
                    <a href="{{ route('Currency') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateCurrency',$data->id) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @method('PUT')
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="countryname">Country Name<span class="required">*</span></label>
                            <input type="text" class="form-control" name="country_name" id="country_name" placeholder="Country Name" value="{{ $data->country_name }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="countrycode">Country Code <span class="required">*</span></label>
                            <input type="text" class="form-control" name="country_code" id="country_code" placeholder="Country Code" value="{{ $data->country_code }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="currencysymbol">currency Symbol <span class="required">*</span></label>
                            <input type="text" class="form-control" name="currency_symbol" id="currency_symbol" placeholder="currency Symbol" value="{{ $data->currency_symbol }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="currencysymbol">Value <span class="required">*</span></label>
                            <input type="text" class="form-control" name="value" id="value" placeholder="currency Symbol" value="{{ $data->value }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <input type="checkbox" id="status" name="status" data-on="Active" data-off="Inactive" class="toggle-class"  placeholder="Status" {{ $data->status=='1' ? 'checked':'' }} />
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')