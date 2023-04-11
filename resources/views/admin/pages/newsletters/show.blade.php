@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>Newsletter</b></div>
						<div class="col col-md-6">
							<a href="{{ route('newsletters.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col mb-3">
							<label class="col-sm-2 col-label-form"><b>Title</b></label>
							<div class="col-sm-10">
								{{ $newsletter->name }}
							</div>
						</div>
						<div class="col mb-3">
							<label class="col-sm-2 col-label-form"><b>Status</b></label>
							<div class="col-sm-10">
								@if($newsletter->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col mb-3">
							<label class="col-sm-2 col-label-form"><b>Subject</b></label>
							<div class="col-sm-10">
								{{ $newsletter->subject }}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col mb-3">
							<label class="col-sm-2 col-label-form"><b>Description</b></label>
							<div class="col-sm-10">
								{!! $newsletter->content !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')