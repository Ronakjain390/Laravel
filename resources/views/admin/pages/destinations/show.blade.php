@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col col-md-6"><b>Destination</b></div>
                        <div class="col col-md-6">
                            <a href="{{ route('destinations.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label><b>Title</b></label>
                            <div> 
                                {{ $destination->title }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label><b>Description</b></label>
                            <div>
                                {!! $destination->description !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label><b>Address</b></label>
                            <div>
                                {{ $destination->address }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <?php foreach (json_decode($destination->image) as $picture) { ?>
                                <img class="img-thumbnail" src="{{ asset('assets/images/destinations/' . $picture) }}" style="height:120px; width:200px; object-fit:cover;" />
                            <?php } ?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('main_content')