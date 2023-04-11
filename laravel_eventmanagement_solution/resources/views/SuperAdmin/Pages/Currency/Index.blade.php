@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5>Currency</h5>
            <a href="{{ route('CreateCurrency') }}" class="btn rounded-pill btn-primary">Create</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Country Name</th>
                        <th>Country Code</th>
                        <th>Currency Symbol</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($data as $index=>$item)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong> {{ $index+1 }} </strong></td>
                        <td>{{ $item->country_name }}</td>
                        <td>
                            {{ $item->country_code }}
                        </td>
                        <td> {{$item->currency_symbol}}  </td>
                        <td> {{$item->value}} </td>
                        <td>
                            @if($item->status ==1)
                            <span class="badge bg-label-primary me-1">Active</span>
                        </td>
                        @else
                        <span class="badge bg-label-warning me-1">InActive</span></td>
                        @endif
                        <td>
                            <a href="{{ route('ShowCurrency', [ 'id' => $item->id ]) }}"><i class="bx bx-show-alt me-1"></i> </a>
                            <a href="{{ route('EditCurrency', [ 'id' => $item->id ]) }}"><i class="bx bx-edit-alt me-1"></i> </a>
                            <a href="{{ route('DeleteCurrency', [ 'id' => $item->id ]) }}" onclick="return confirm('Are you sure you want to delete this item?')"><i class="bx bx-trash me-1"></i> </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')