@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
<div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-email"></i>
                </span> Edit
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('smtpmail.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" action="{{ route('smtpmail.update',$data->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-6 form-group">
                <label for="host">Host</label>
                <input type="text" id="host" name="host" class="form-control" value="{{ $data->host }}">
            </div>
            <div class="col-md-6 form-group">
                <label for="port">Port</label>
                <input type="text" id="port" name="port" class="form-control" value="{{ $data->port }}">
            </div>
            <div class="col-md-6 form-group">
                <label for="userName">User Name</label>
                <input type="text" id="userName" name="userName" class="form-control" value="{{ $data->userName }}">
            </div>
            <div class="col-md-6 form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" value="{{ $data->password }}">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success btn-lg float-right px-5 mt-5">Submit</button>
        </div>
    </form>
</div>
@endsection('main_content')