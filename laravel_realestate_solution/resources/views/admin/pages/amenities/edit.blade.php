@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-pool"></i>
                </span> Edit Amenity
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('amenities.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" action="{{ route('amenities.update',$amenities->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-12 form-group">
                <label for="amenities">Amenities <span class="required">*</span></label>
                <textarea name="amenities" id="amenities" class="form-control">{{ $amenities->amenities }}</textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="status">Status</label><br>
                <input type="checkbox" id="status" name="status" {{ $amenities->status=='1' ? 'checked':'' }}>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        $('#status').bootstrapToggle({
            on: 'Active',
            off: 'InActive'
        });
    })
</script>
@endsection('main_content')