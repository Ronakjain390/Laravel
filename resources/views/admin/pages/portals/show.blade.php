@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>CMS</b></div>
						<div class="col col-md-6">
							<a href="{{ route('portals.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">View All</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-4">
							<label class="col-label-form"><b>Title</b></label>
							<div>
								{{ $portal->name }}
							</div>
						</div>
						<div class="col-sm-4">
							<label class="col-label-form"><b>Slug</b></label>
							<div>
								{{ $portal->slug }}
							</div>
						</div>
						<div class="col-sm-4">
							<label class=" col-label-form"><b>Status</b></label>
							<div>
								@if($portal->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>
						<div class="col-sm-12 pt-4">
							<label class="col-label-form"><b>Content</b></label>
							<div>
								{!!$portal->content_editor!!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')