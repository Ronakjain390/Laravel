@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>News Details</b></div>
						<div class="col col-md-6">
							<a href="{{ route('news.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<label><b>Title</b></label>
							<div>
								{{ $news->title }}
							</div>
						</div>
						<div class="col-md-3">
							<label><b>Status</b></label>
							<div>
								@if($news->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>
					</div>
					<div class="row mt-2">
						<div>
							<img src="{{ asset('assets/images/news/' . $news->image) }}" width="200" class="img-thumbnail" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 pt-3">
							<div>
								{!! $news->description !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')