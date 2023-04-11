@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>Reviews and Rating </b></div>
						<div class="col col-md-6">
							<a href="{{ route('reviewsratings.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<label><b>Title</b></label>
							<div>
								{{ $reviewsrating->name }}
							</div>
						</div>
						<div class="col-md-3">
							<label><b>Rating</b></label>
							<div>
								@if($reviewsrating->rating == '')
								Null
								@else
								{{ $reviewsrating->rating }}
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<label><b>Approve/Reject</b></label>
							<div>
								{{ $reviewsrating->is_approve }}
							</div>
						</div>
						<div class="col-md-3">
							<label><b>Status</b></label>
							<div>
								@if($reviewsrating->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>
					</div>
					<div class="row mt-2">
						<div>
							<img src="{{ asset('assets/images/reviewrating/' . $reviewsrating->image) }}" width="200" class="img-thumbnail" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 pt-3">
							<div>
								{!! $reviewsrating->reviews !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')