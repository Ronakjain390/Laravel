@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5>Reviews and Ratings</h5>
            <a href="{{ route('CreateReviewsRatings') }}" class="btn rounded-pill btn-primary">Create</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Reviews</th>
                        <th>Rating</th>
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
                        {{ $item->reviews }}
                        </td>
                        <td>
                        {{ $item->rating }}
                        </td>
                        <td>
                            @if($item->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span></td>
                            @else
                            <span class="badge bg-label-warning me-1">InActive</span></td>
@endif
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a  href="{{ route('ShowReviewsRatings', [ 'id' => $item->id ]) }}"><i class="bx bx-show-alt me-1"></i> Show</a>
                                    <a  href="{{ route('EditReviewsRatings', [ 'id' => $item->id ]) }}"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                    <a  href="{{ route('DeleteReviewsRatings', [ 'id' => $item->id ]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')