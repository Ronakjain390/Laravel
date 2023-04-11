@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
        <h4> <b> Events</b> </h4>
            <a type="button" class="btn btn-primary" href="{{ route('createevents') }}">Create Event</a>
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
                        <th scope="col">Sr.No</th>
                        <th scope="col">Name</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Verified</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(count($event)>0)
                    @foreach($event as $index=>$data)
                    <tr>
                        <th scope="row">{{ $index+1 }}</th>
                        <td>{{ $data->title }}</td>
                        <td>{{date('D, d M Y', strtotime($data->start_date)) }}</td>
                        <td>{{date('D, d M Y', strtotime($data->end_date ))}}</td>
                        <td>{{ $data->is_varified == 0 ? 'No' : 'Yes' }}</td>
                        <td>{{ $data->status == 1 ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('TicketShow', [ 'id' => $data->id ]) }}"><i class="bi bi-ticket-detailed-fill"></i> </a>
                            <a href="{{ route('showevent', [ 'id' => $data->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                            <a href="{{ route('eventedit', [ 'id' => $data->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('eventdelete', ['id' => $data->id]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                        </td>
                      
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="8" class="ndata">There is no Event available.Please create something....</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            </section>
        </div>
    </div>
</div>


<style>
    .header22 a {
        float: right;
    }

    .ndata {
        text-align: center;
    }
</style>
@endsection('content')