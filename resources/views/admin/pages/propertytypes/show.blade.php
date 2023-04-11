@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>Property Type Details</b></div>
						<div class="col col-md-6">
							<a href="{{ route('propertytypes.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3 px-3">
							<label><b>Name</b></label>
							<div>
								{{ $ptype->name }}
							</div>
						</div>
						<div class="col-md-3 px-3">
							<label><b>Status</b></label>
							<div>
								@if($ptype->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>
					</div>
					<div class="row mt-2">
						<div class="px-3">
						<img src="{{ asset('assets/images/propertytype/' . $ptype->image) }}" width="100" class="img-thumbnail" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 pt-3 px-3">
							<div>
								{!! $ptype->description !!}
							</div>
							<div>
								{!! $ptype->category_name !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')