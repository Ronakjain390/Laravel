@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="">Event Organizer</h5>
            <!-- <a href="{{ route('CreateEventOrganizer') }}" class="btn rounded-pill btn-primary">Create</a> -->
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($eventOrganizerData as $index=>$OrganizerData)
                    <tr>
                        <td> {{ $index+1 }} </td>
                        <td>
                            <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up me-2" title="Lilian Fuller">
                                    {{$OrganizerData->name}}
                                </li>
                            </ul>
                        </td>
                        <td>{{ $OrganizerData->email }}</td>
                        <td> {{ $OrganizerData->contact }}</td>
                        <td>
                            @if($OrganizerData->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>

                            @else
                            <span class="badge bg-label-warning me-1">InActive</span>
                        </td>
                        @endif
                        <td>
                            <a href="{{ route('ShowEventOrganizer', ['id' => $OrganizerData->id ]) }}"><i class="bx bx-show-alt me-1"></i></a>
                            <!-- <a href="{{ route('EditEventOrganizer', ['id' => $OrganizerData->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a> -->
                            <a href="{{ route('DeleteEventOrganizer', ['id' => $OrganizerData->id ]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')