@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
	<div class="row">
		<div class="col-xl">
			<div class="card ">
				<div class="card-header">
					<h5 class="">Banner Details</h5>
					<a href="{{ route('Banners') }}" class="btn rounded-pill btn-primary">Back</a>
				</div>


				@foreach($data as $item)
				<div class="card-body">
					<div class="row">
						<div class="col-sm-6">
							<label class="col-label-form"><b>Name</b></label>
							<div>
								{{ $item->title }}
							</div>
						</div>
						<div class="col-sm-6">
							<label class=" col-label-form"><b>Description</b></label>
							<div>
								{!!$item->description!!}
							</div>
						</div>
					</div>

					<div class="row">
					<div class="col-sm-6">
						<label class=" col-label-form"><b>Image</b></label>
						<div>
							<img src="{{ asset('Assets/images/' . $item->image) }}" style="height:120px; width:200px" />
						</div>
					</div>
					<div class="col-sm-6">
						<label class="col-label-form"><b>Status</b></label>
						<div>
							@if($item->status ==1)
							<span class="badge bg-label-primary me-1">Active</span>
							@else
							<span class="badge bg-label-warning me-1">InActive</span>
							@endif
						</div>
					</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</div>
@endsection('SuperAdminContent')