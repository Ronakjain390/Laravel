@extends('admin/layouts/layout/base')
@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-feather"></i>
                </span> Send Newsletter
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('newsletters.index') }}" class="btn btn-success btn-lg float-right">Back</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    @if(session('status'))
    <div class="alert alert-success mb-1 mt-1">
        {{ session('status') }}
    </div>
    @endif
    <form action="{{ route('send-newsletters.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-2">
            <div class="col-md-6 form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control">
            </div>
            <div class="col-md-6 form-group">
                <label>Newsletter Template</label>
                <select id="template" name="template" class="form-control">
                    <option value="">-- Select Template --</option>
                    @foreach ($temp as $data)
                    <option value="{{$data->id}}">
                        {{$data->name}}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-md-6 form-group">
                <label>Subscribed User:</label>
                <select id="subscribed_user" multiple="multiple" name="subscribed_user[]" class="form-control">
                    <option value="">-- Select Subscribed User --</option>
                    @foreach ($sub as $data)
                    <option value="{{$data->id}}" selected>
                        {{$data->email}}
                    </option>
                    @endforeach
                </select>
                @error('subscribed user')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
        </div>
    </form>
</div>

@endsection('main_section')