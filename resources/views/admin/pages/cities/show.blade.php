@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>City </b></div>
						<div class="col col-md-6">
							<a href="{{ route('cities.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<label><b>City Name</b></label>
							<div>
								{{ $city->city }}
							</div>
						</div>
						<div class="col-md-3">
							<label><b>State Name</b></label>
							
							<div>
								{{ $state[0]->statename }}
							</div>
							
						</div>
						<div class="col-md-3">
							<label><b>Country Name</b></label>
							
							<div>
								{{$country[0]->countryname}}
							</div>
							
						</div>
						<div class="col-md-3">
							<label><b>Status</b></label>
							<div>
								@if($city->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')