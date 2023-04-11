@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
	<div class="row">
		<div class="col-xl">
			<div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h4>Artist's Expertize Detail </h4>
					<div class="col col-md-6">
						<a href="{{ route('OrganizerArtistExpertize') }}" class="btn btn-primary btn-sm float-end">Back</a>
					</div>
				</div>

				@foreach($data as $item)
				<div class="card-body mt-3">
					<div class="row">
						<div class="mb-3 col-sm-6">
							<label class="col-label-form"><b>Expertize</b></label>
							<div>
								{{ $item->name }}
							</div>
						</div>
						<div class="mb-3 col-sm-6">
							<label class="col-label-form"><b>Status</b></label>
							<div>
								@if($item->status ==1)
								<span class="badge bg-label-primary me-1">Active</span>
								@else
								<span class="badge bg-label-warning me-1">InActive</span>
								@endif
							</div>
						</div>
						<div class="mb-3 col-sm-12">
							<label class=" col-label-form"><b>Description</b></label>
							<div>
								{!!$item->description!!}
							</div>
						</div>

						<div class="mb-3 col-sm-6">
							<label class=" col-label-form"><b>Expertize Image</b></label>
							<div>
								<img src="{{ asset('Assets/images/' . $item->image) }}" style="height:120px; width:200px" />
							</div>
						</div>

					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</div>
@endsection('content')