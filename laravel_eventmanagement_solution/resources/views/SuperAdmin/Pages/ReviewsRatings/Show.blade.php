@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
	<div class="row">
		<div class="col-xl">
			<div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
					<div class="col col-md-6"><b>Reviews/ Ratings Details</b></div>
					<div class="col col-md-6">
						<a href="{{ route('ReviewsRatings') }}" class="btn btn-primary btn-sm float-end">Back</a>
					</div>
				</div>

				@foreach($data as $item)
				<div class="card-body">
					<div class="row mb-3">
						<label class="col-label-form"><b>Name</b></label>
						<div>
							{{ $item->name }}
						</div>
					</div>
					<div class="row mb-3">
						<label class=" col-label-form"><b>Reviews</b></label>
						<div>
							{!!$item->reviews!!}
						</div>
					</div>
                    <div class="row mb-3">
						<label class=" col-label-form"><b>Approval</b></label>
						<div>
							{!!$item->is_approve!!}
						</div>
					</div>
                    <div class="row mb-3">
						<label class=" col-label-form"><b>Image</b></label>
						<div>
                        <img src="{{ asset('Assets/SuperAdmin/img/ReviewsRatings/' . $item->image) }}" style="height:120px; width:200px" />

						</div>
					</div>
                    <div class="row mb-3">
						<label class=" col-label-form"><b>rating</b></label>
						<div>
							{!!$item->rating!!}
						</div>
					</div>
                    <div class="row mb-3">
						<label class="col-label-form"><b>Status</b></label>
						<div>
							@if($item->status == 1)
							<input type="checkbox" id="status" name="status" checked disabled>
							@else
							<input type="checkbox" id="status" name="status" disabled>
							@endif
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</div>
@endsection('SuperAdminContent')