@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Event's Category</h5>
            <a href="{{ route('CreateEventCategory') }}" class="btn rounded-pill btn-primary">Create</a>
        </div>
        @if (Session::has('success'))
        <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" id="message" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @elseif(Session::has('error'))
        <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" id="message" role="alert">
            {{ Session::get('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Created on</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($eventCategoryData as $index=>$eventCategoryData)
                    <tr>
                        <td> {{ $index+1}} <strong></strong></td>
                        <td>
                            <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up me-2" title="Lilian Fuller">

                                    <img src="{{ asset('Assets/images/' . $eventCategoryData->image) }}" alt="Avatar" class="rounded-circle" />

                                    {{$eventCategoryData->category_name}}
                                </li>
                            </ul>
                        </td>
                        <td> {{ date('d M Y', strtotime($eventCategoryData->created_at)); }}</td>

                        <td>
                            @if($eventCategoryData->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>

                            @else
                            <span class="badge bg-label-warning me-1">InActive</span>
                        </td>
                        @endif
                        <td>
                            <a href="{{ route('ShowEventCategory', ['id' => $eventCategoryData->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                            <a href="{{ route('EditEventCategory', ['id' => $eventCategoryData->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteEventCategory', ['id' => $eventCategoryData->id ]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection('SuperAdminContent')