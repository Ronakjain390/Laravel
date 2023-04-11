@extends('agent/layouts/master')

@section('content')
<div class="container my-5">
    @if (Session::has('Success'))
    <div class="alert alert-danger">
        <p>{{ Session::get('Success') }}</p>
    </div>
    @endif
    @if(Session::has('success'))
    <div class="alert alert-success">
        <p>{{ Session::get('success') }}</p>
    </div>
    @endif
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">
                <span></span> <a href="{{ route('property.create') }}" class="btn btn-outline-success btn-lg float-right">Create</a>
            </li>
        </ul>
    </nav>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">S.NO</th>
                <th scope="col">Name</th>
                <th scope="col">Contact Number</th>
                <th scope="col">Action</th>

            </tr>
        </thead>
        <tbody>
            @foreach($property as $index=>$item)

            <tr>
                <th scope="row">{{$loop->index + 1}}</th>
                <td>{{$item->name}}</td>
                <td>{{$item->contactNumber}}</td>
                <td class="statusAgent">
                    <a href="{{route('property.show', $item->id)}}" class="btn btn-success mr-2">
                        <i class='fa fa-eye'></i>
                    </a>
                    <a href="{{route('property.edit',  $item->id)}}" class="btn btn-primary">
                        <i class='fa fa-edit'></i>
                    </a>
                    <a href="{{ route('propertyDestroy',$item->id) }}" class="btn btn-danger">
                        <i class='fa fa-trash'></i>
                    </a>
                    <form method="POST" action="{{ route('propertyStatus', $item->id) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn {{ $item->status == 1 ? 'btn-info' : 'btn-danger'}}" name="action" value="status_toggle">
                            <i class="fa {{ $item->status == 1 ? 'fa-check' : 'fa-ban' }}"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
<style>
    .statusAgent {
        display: flex;
    }

    .statusAgent a {
        margin-left: 5px;
    }

    .statusAgent form {
        margin-left: 5px;
    }
</style>
@endsection('content')