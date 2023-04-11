@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card">
                <div class="card-header">
                    <h5 class="">Templates Details</h5>
                    <a href="{{ route('Templates') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                @foreach($data as $item)
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="col-label-form"><b>Name</b></label>
                            <div>
                                {{ $item->name }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-label-form"><b>Subject</b></label>
                            <div>
                                {{ $item->subject }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class=" col-label-form"><b>Content</b></label>
                            <div>
                                {!!$item->content!!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="col-label-form"><b>Status</b></label>
                            <div>
                                @if($item->status ==1)
                                <span class="badge bg-label-primary me-1">Active</span>
                                @else
                                <span class="badge bg-label-warning me-1">InActive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
    @endsection('SuperAdminContent')