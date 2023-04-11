@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-email"></i>
                </span> SMTP Details
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('smtpmail.edit', $data[0]->id) }}" class="btn btn-success btn-lg float-right">Edit</a>
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
    <div class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Host</b></label>
                        <div class="col-sm-10">
                            {{ $data[0]->host }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Port</b></label>
                        <div class="col-sm-10">
                            {{ $data[0]->port }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>User Name</b></label>
                        <div class="col-sm-10">
                            {{ $data[0]->userName }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Password</b></label>
                        <div class="col-sm-10">
                            {{ $data[0]->password }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('main_content')