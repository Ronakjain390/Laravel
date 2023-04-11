@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-currency-usd"></i>
                </span> Edit
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('currencies.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" action="{{ route('currencies.update',$currency->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="form-group col-md-4">
                <label for="country_name">Country Name</label>
                <input type="text" class="form-control" id="country_name" name="country_name" value="{{ $currency->country_name }}" />
            </div>
            <div class="form-group col-md-4">
                <label for="country_code">Country Code</label>
                <input type="text" class="form-control" id="country_code" name="country_code" value="{{ $currency->country_code }}" />
            </div>
            <div class="form-group col-md-4">
                <label for="currency_symbol">Currency Symbol</label>
                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ $currency->currency_symbol }}" />
            </div>
            <div class="form-group col-md-4">
                <label for="value">Value</label>
                <input type="text" class="form-control" id="value" name="value" value="{{ $currency->value }}" />
            </div>
            <div class="form-group col-md-4">
                <label for="default">Set Currency as Default</label><br />
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="default" name="default" {{ $currency->default == 1 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="default"></label>
                </div>
            </div>
            <div class="form-group col-md-3">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" {{ $currency->status == 1 ? 'checked' : '' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>

@endsection('main_content')