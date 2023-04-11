@extends('agent/layouts/master')

@section('content')
<div class="container my-5">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">S.NO</th>
                <th scope="col">Name</th>
                <th scope="col">Subject</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enquiry as $index=>$item)
            <tr>
                <th scope="row">{{$loop->index + 1}}</th>
                <td>{{$item->name}}</td>
                <td>{{$item->description}}</td>
                <td> <a href="{{route('enquiryShow', $item->id)}}" class="btn btn-success">
                        <i class='fa fa-eye'></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection('content')