@extends('agent/layouts/master')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col col-md-6 d-flex align-items-center"><b>Agent Enquiry</b></div>
                <div class="col col-md-6">
                    <a href="{{ url('agent/agentenquiry') }}" class="btn btn-outline-primary btn-sm float-end">Back</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">

                <div class="col-sm-4 mb-3">

                    <label class="col-label-form"><b>Name</b></label>
                    <div>
                        {{ $pro->name }}
                    </div>
                </div>

                <div class="col-sm-4 mb-3">

                    <label class="col-label-form"><b>Email</b></label>
                    <div>
                        {{ $pro->email }}
                    </div>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="col-label-form"><b>Contact</b></label>

                    <div>
                        {{ $pro->contact }}
                    </div>
                </div>
                <div class="row mb-12">
                    <label class=" col-label-form"><b>Description</b></label>
                    <div>
                        {{ $pro->description }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')