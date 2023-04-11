@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>FAQ Details</b></div>
						<div class="col col-md-6">
							<a href="{{ route('faqs.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row mb-3">
						<label class="col-label-form"><b>Question</b></label>
						<div>
							{{ $faq->question }}
						</div>
					</div>
					<div class="row mb-3">
						<label class=" col-label-form"><b>Answer</b></label>
						<div>
							{!!$faq->answer!!}
						</div>
					</div>
					<div class="row mb-3">
						<label class="col-label-form"><b>Status</b></label>
						<div>
							@if($faq->status == 1)
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
@endsection('main_content')