@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5>Contact Enquiries</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Contact Detail</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($data as $index=>$item)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong> {{ $index+1 }} </strong></td>
                        <td>{{ $item->name }}</td>
                        <td>
                            {{ Str::length($item->subject) > 20 ? substr($item->subject, 0,15) . '...' : $item->subject }}
                        </td>
                        <td>
                            @if($item->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>
                        </td>
                        @else
                        <span class="badge bg-label-warning me-1">InActive</span></td>
                        @endif
                        <td>
                            <a  href="{{ route('ShowContactEnquiry', [ 'id' => $item->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')