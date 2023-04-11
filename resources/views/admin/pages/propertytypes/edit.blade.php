@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-newspaper"></i>
                </span> Edit Property Type
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('propertytypes.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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
    <form method="POST" action="{{ route('propertytypes.update',$ptype->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-12  form-group">
                <label for="title">Name</label>
                <input type="text" class="form-control" name="name" value="{{ $ptype->name }} " />
            </div>
            <div class="col-md-3 form-group">
                <label for="reason">Category<span class="required">*</span></label>
                
                <select class="form-control @error('category') is-invalid @enderror" name="propertycategory" id="propertycategory">
                    <option selected>--Category--</option>
                    @foreach($category as $item)
                    <option value="{{ $item->id }}">{{$item->category_name}}</option>
                    @endforeach
                </select>
                @error('reason')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 form-group">
                <label for="descriptiom">Description</label>
                <textarea name="description" id="content" class="form-control">{{ $ptype->description }}</textarea>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6 form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control" name="image" value="c:kjs" />
                <img src="{{ asset('assets/images/propertytype/' . $ptype->image) }}" width="100" class="mt-1 img-thumbnail" />
                <input type="hidden" name="hidden_Image" value="{{ $ptype->image }}" />
                @error('Image')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" placeholder="Status" {{ $ptype->status=='1' ? 'checked':'' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>

    </form>
</div>

@endsection('main_content')