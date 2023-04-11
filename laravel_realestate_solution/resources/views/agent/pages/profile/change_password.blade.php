@extends('agent/layouts/master')

@section('content')
<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title text-center"><br>
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-account"></i>
                    </span> Update Passwrod
                </h3>
                <nav class="px-3" aria-label="breadcrumb">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item active" aria-current="page">
                            <span></span> <a href="{{ route('agentProfile') }}" class="btn btn-outline-success btn-lg float-end">Back</a>
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

        <div class="container">
            <form class="form bg-white p-4 rounded shadow" method="POST" action="{{ route('agent_paas_change') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row margin_top">
                    <div class="col-md-6 form-group">
                        <label for="name">Old Password</label>
                        <input type="text" class="form-control" name="oldpassword" />
                    </div>
                </div>
                <div class="row margin_top">
                    <div class="col-md-6 form-group">
                        <label for="name">New Password</label>
                        <input type="text" class="form-control" name="newpassword" />
                    </div>
                </div>
                <div class="row margin_top">
                    <div class="col-md-6 form-group">
                        <label for="name">Confirm Password</label>
                        <input type="text" class="form-control" name="confirmpasswrod" />
                    </div>
                </div>
                <div class="row margin_top">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-lg float-right text-white px-5">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .margin_top {
        margin-top: 10px;
    }
</style>
@endsection('content')