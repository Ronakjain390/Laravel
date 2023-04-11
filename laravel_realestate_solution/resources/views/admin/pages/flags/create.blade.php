@extends('admin/layouts/layout/base')
@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-newspaper"></i>
                </span> Create Flag
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('Flags') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="post" action="{{ route('flagstore') }}">
        @csrf
        <div class="row mt-2">
            <div class="col-md-6 form-group">
                <label for="title">Flag Name <span class="required">*</span></label>
                <input type="text" class="form-control @error('flagname') is-invalid @enderror" name="flag_name" id="flag_name" />
                @error('flag_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
            </div>
    </form>
</div>
@endsection('main_content')