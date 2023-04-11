@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col col-md-6"><b>Contact Enquiry </b></div>
                        <div class="col col-md-6">
                            <a href="{{ route('contacts.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label><b>Name</b></label>
                            <div>
                                {{ $data->name }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label><b>Email</b></label>

                            <div>
                                {{ $data->email }}
                            </div>

                        </div>
                        <div class="col-md-3">
                            <label><b>Contact Number</b></label>

                            <div>
                                {{ $data->contact_details }}
                            </div>

                        </div>
                        <div class="col-md-3">
                            <label><b>Subject</b></label>

                            <div>
                                {{$data->subject}}
                            </div>

                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label><b>Description</b></label>

                            <div>
                                {{$data->description}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('main_content')