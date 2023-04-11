@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col col-md-6"><b>Banner</b></div>
                        <div class="col col-md-6">
                            <a href="{{ route('banners.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label><b>Title</b></label>
                            <div>
                                {{ $banner->title }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label><b>Status</b></label>
                            <div>
                                @if($banner->status == 1)
                                <input type="checkbox" id="status" name="status" checked disabled>
                                @else
                                <input type="checkbox" id="status" name="status" disabled>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <img src="{{ asset('assets/images/banner/' . $banner->image) }}" width="200" class="img-thumbnail" />
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 pt-3">
                            {!!$banner->description!!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('main_content')