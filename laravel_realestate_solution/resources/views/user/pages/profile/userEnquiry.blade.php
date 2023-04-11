@extends('user/layouts/profileLayout/baseProfile')
@section('baseprofile')
<div class="content-wrapper">
	<!-- <div class="">
		@include('user/layouts/bannerSlider/bannerSlider')
	</div><br> -->
	<h1 class="heading-title">Enquiries</h1>
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-body">
					@if($enquiry)
					@foreach($enquiry as $index=>$enquiry)
					<div class="row">
						<div class="col-md-1">
							<p>{{$loop->index + 1}}.</p>
						</div>
						<div class="col-md-3">
							<label><b> Property Name</b></label>
							<div>
								{{$propertyname->title}}
							</div>
						</div>
						<div class="col-md-3">
							<label><b>Enquiry User Name</b></label>
							<div>
								{{ $enquiry->name }}
							</div>
						</div>
						<div class="col-md-3">
							<label><b>Email</b></label>
							<div>
								{{ $enquiry->email }}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 pt-3">
						</div>
						<div class="col-md-3">
							<label><b>Contact</b></label>
							<div>
								{{ $enquiry->contact }}
							</div>
						</div>
						<div class="col-md-8 pt-3">
							<label><b>Description</b></label>
							<div>
								{!! $enquiry->description !!}
							</div>
						</div>
					</div>
					<br><br>
					@endforeach
					@else
					<p>No Enquiry.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('baseprofile')