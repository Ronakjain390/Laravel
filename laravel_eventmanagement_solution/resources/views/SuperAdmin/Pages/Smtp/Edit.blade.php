@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit FAQ's</h5>
                    <a href="{{ route('Faqs') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('UpdateSmtp',$data->id) }}" enctype="multipart/form-data">
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


                            <button type="submit" class="btn btn-success btn-lg float-right px-5 mt-5">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')