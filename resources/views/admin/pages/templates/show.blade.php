@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>Email Templates </b></div>
						<div class="col col-md-6">
							<a href="{{ route('templates.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<label><b>Title</b></label>
							<div>
								{{ $template->name }}
							</div>
						</div>
						<div class="col-md-3">
							<label><b>Status</b></label>
							<div>
								@if($template->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>

						<div class="col-md-12 pt-3">
							<label><b>Subject</b></label>
							<div>
								{{ $template->subject }}
							</div>
						</div>

						<div class="col-md-12 pt-3">
							<label><b>Content</b></label>
							<div>
								{!!$template->content!!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')