@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5>Un-Verified Events</h5>
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
                        <th>Event Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(count($events)>0)
                    @foreach($events as $index=>$item)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong> {{ $index+1 }} </strong></td>
                        <td>{{ $item->title }}</td>
                        <td>
                            {{ date('D, d M Y', strtotime($item->start_date)) }}
                        </td>
                        <td>
                            {{date('D, d M Y', strtotime($item->end_date ))}}
                        </td>
                        <td>
                            {{ $item->start_time }}
                        </td>
                        <td>
                            {{ $item->end_time }}
                        </td>

                        <td>
                            <a href="{{ route('verify_event', $item->id) }}"><i class="bx bxs-user-check me-1"></i></a>
                            <a href="{{ route('show_event', $item->id) }}" > <i class="bx bx-show-alt me-1"></i></a>
                            <a href="{{ route('delete_event', $item->id) }}"><i class="bx bx-trash-alt me-1"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <td colspan="7" class="no_event">No. Events avalable for Verification.</td>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .no_event {
        text-align: center;
    }
</style>
@endsection('SuperAdminContent')