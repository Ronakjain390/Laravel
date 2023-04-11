@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>User Detail</b></div>
						<div class="col col-md-6">
							<a href="{{ route('users.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-4">

							<div class="row mb-3">
								<label class="col col-label-form"><b>Name</b></label>
								<div class="col-sm-10">
									{{ $user->name }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col col-label-form"><b>Email</b></label>
								<div class="col-sm-10">
									{{ $user->email }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col col-label-form"><b>Conatct Number</b></label>
								<div class="col-sm-10">
									{{ $user->contact }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col col-label-form"><b>User Name</b></label>
								<div class="col-sm-10">
									{{ $user->username }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col col-label-form"><b>Address</b></label>
								<div class="col-sm-10">
									{{ $user->address }}
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="row mb-3">
								<label class="col col-label-form"><b>City</b></label>
								<div class="col-sm-10">
									{{ $user->city }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col col-label-form"><b>State</b></label>
								<div class="col-sm-10">
									{{ $user->state }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col col-label-form"><b>Country</b></label>
								<div class="col-sm-10">
									{{ $user->country }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col col-label-form"><b>Postal Code</b></label>
								<div class="col-sm-10">
									{{ $user->postal_code }}
								</div>
							</div>
						
							<div class="row mb-3">
								<label class="col col-label-form"><b>Status</b></label>
								<div class="col-sm-10">
									@if($user->status == 1)
									<input type="checkbox" id="status" name="status" checked disabled>
									@else
									<input type="checkbox" id="status" name="status" disabled>
									@endif
								</div>
							</div>
							</div>
						<div class="col-sm-4">
								<div class="row mb-3">
									<label class="col col-label-form"><b>Profile Picture</b></label>
									<div class="col-sm-10">
										<img src="{{ asset('assets/images/users/' . $user->image) }}" width="200" class="img-thumbnail" />
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