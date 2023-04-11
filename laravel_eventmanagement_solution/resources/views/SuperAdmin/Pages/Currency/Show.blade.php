@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
	<div class="row">
		<div class="col-xl">
			<div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
					<div class="col col-md-6"><b>Currency Details</b></div>
					<div class="col col-md-6">
						<a href="{{ route('Currency') }}" class="btn btn-primary btn-sm float-end">Back</a>
					</div>
				</div>

				@foreach($data as $item)
				<div class="card-body">
					<div class="row">
						<div class="col-sm-6">
							<label class="col-label-form"><b>Country Name</b></label>
							<div>
								{{ $item->country_name }}
							</div>
						</div>
						<div class="col-sm-6">
							<label class=" col-label-form"><b>Country Code</b></label>
							<div>
								{!!$item->country_code!!}
							</div>
						</div>
						<div class="col-sm-6">
							<label class=" col-label-form"><b>Currency Symbol</b></label>
							<div>
								{!!$item->currency_symbol!!}
							</div>
						</div>
						<div class="col-sm-6">
							<label class=" col-label-form"><b>Currency Value</b></label>
							<div>
								{{$item->value}}
							</div>
						</div>

						<div class="col-sm-3">
							<label class="col-label-form"><b>Status</b></label>
							<div>
								@if($item->status ==1)
								<span class="badge bg-label-primary me-1">Active</span>
								@else
								<span class="badge bg-label-warning me-1">InActive </span>
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