@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h4>Food Stores</h4>
            <a href="{{ route('CreateFoodStoresOrganizer') }}" class="btn rounded-pill btn-primary">Create</a>
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
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(count($data)>0)
                    @foreach($data as $index=>$item)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong> {{ $index+1 }} </strong></td>
                        <td>{{ $item->name }}</td>
                        <td>
                           
                            {!! Str::length($item->description) > 30 ? substr($item->description, 0,40) . '...' : $item->description !!}
                        </td>
                        <td>
                            @if($item->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>
                        </td>
                        @else
                        <span class="badge bg-label-warning me-1">InActive</span></td>
                        @endif

                        <td>
                            <a href="{{ route('ShowFoodStoreOrganizer', [ 'id' => $item->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                            <a href="{{ route('EditFoodStoreOrganizer', [ 'id' => $item->id ]) }}" ><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteFoodStoreOrganizer', ['id' => $item->id]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                        </td>
                    </tr>
                     @endforeach
                    @else
                    <tr>
                        <td colspan="8" class="ndata">There is no FoodStores available. Please create ...</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection('content')