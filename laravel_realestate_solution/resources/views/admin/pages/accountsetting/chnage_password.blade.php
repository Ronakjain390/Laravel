@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account"></i>
                </span> Update Password
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('accountsetting.index') }}" class="btn btn-success btn-lg float-right">Back</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        <p>{{ $message }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('admin_paas_change') }}">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-3 form-group">
                <label for="name">Old Password</label>
                <input type="text" class="form-control" name="oldpassword" />
            </div>
            <div class="col-md-3 form-group">
                <label for="email">New Password</label>
                <input type="text" class="form-control" name="newpassword" />
            </div>
            <div class="col-md-3 form-group">
                <label for="contact">Confirm Password</label>
                <input type="text" class="form-control" name="confirmpasswrod" />
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')