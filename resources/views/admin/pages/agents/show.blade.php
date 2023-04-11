@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>Agent Detail</b></div>
						<div class="col col-md-6">
							<a href="{{ route('agents.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">View All</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-4">
							<div class="row mb-3">
								<label class="col-label-form"><b>Name</b></label>
								<div>

									{{ $agent->name }}

								</div>
							</div>
							<div class="row mb-3">
								<label class="col-label-form"><b>Email</b></label>
								<div>

									{{ $agent->email }}

								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Conatct Number</b></label>
								<div>

									{{ $agent->contact }}

								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>User Name</b></label>
								<div>

									{{ $agent->username }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Address</b></label>
								<div>
									{{ $agent->address }}

								</div>
							</div>
						</div>
						<div class="col-sm-4">

							<div class="row mb-3">
								<label class=" col-label-form"><b>City</b></label>
								<div>

									{{ $agent->city }}

								</div>
							</div>


							<div class="row mb-3">
								<label class=" col-label-form"><b>State</b></label>
								<div>

									{{ $agent->state }}

								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Country</b></label>
								<div>
									{{ $agent->country }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Postal Code</b></label>
								<div>

									{{ $agent->postal_code }}

								</div>
							</div>

							<div class="row mb-3">
								<label class=" col-label-form"><b>Status</b></label>
								<div>
									@if($agent->status == 1)
									<input type="checkbox" id="status" name="status" checked disabled>
									@else
									<input type="checkbox" id="status" name="status" disabled>
									@endif
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="row mb-3">
								<label class=" col-label-form"><b>Profile Picture</b></label>
								<div>
									<img src="{{ asset('assets/images/agents/'.$agent->image) }}" width="200" class="img-thumbnail" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')