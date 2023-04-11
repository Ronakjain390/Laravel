@extends('admin/layouts/layout/base')
@section('main_content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6"><b>Property Manager</b></div>
						<div class="col col-md-6">
							<a href="{{ route('propertymanager.index') }}" class="btn btn-primary rounded-1 btn-sm float-end">Back</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-4">
							<div class="row mb-3">
								<label class="col-label-form"><b>Name</b></label>
								<div>
									{{ $property->name }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col-label-form"><b>Email</b></label>
								<div>
									{{ $property->email }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Conatct Number</b></label>
								<div>
									{{ $property->contactNumber }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Property Title</b></label>
								<div>
									{{ $property->title }}
								</div>
							</div>
							@if($ownerName)
							<div class="row mb-3">
								<label class=" col-label-form"><b>Owner Name</b></label>
								<div>
									{{ $ownerName->name }}
								</div>
							</div>
							@endif
							@if($agentName)
							<div class="row mb-3">
								<label class=" col-label-form"><b>Agent Name</b></label>
								<div>
									{{ $agentName->name }}
								</div>
							</div>
							@endif
							<div class="row mb-3">
								<label class=" col-label-form"><b>User Type</b></label>
								<div>
									{{ $property->Utype }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Type</b></label>
								<div>
									{{ $property->reason }}
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="row mb-3">
								<label class=" col-label-form"><b>Type of Price</b></label>
								<div>
									{{ $property->priceType }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Total Price</b></label>
								<div>
									{{ $property->totalPrice }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>User Type</b></label>
								<div>
									{{ $property->Utype }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Type</b></label>
								<div>
									{{ $property->reason }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col-label-form"><b>Bedroom</b></label>
								<div>
									{{ $property->bedroom }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col-label-form"><b>Bathroom</b></label>
								<div>
									{{ $property->bathroom }}
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="row mb-3">
								<label class=" col-label-form"><b>Address</b></label>
								<div>
									{{ $property->address }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>City</b></label>
								<div>
									{{ $property->city }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>State</b></label>
								<div>
									{{ $property->state }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>Country</b></label>
								<div>
									{{ $property->country }}
								</div>
							</div>
							<div class="row mb-3">
								<label class="col-label-form"><b>Postal Code</b></label>
								<div>
									{{ $property->postal_code }}
								</div>
							</div>
							<div class="row mb-3">
								<label class=" col-label-form"><b>General Amenities</b></label>
								<div>
									@if(count($amenities)>0)
									{{ $amenities[0]->amenities }}
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 mb-3">
							<label><b>Property Category</b></label>
							<div>
								@if(count($propertytype)>0)
								{{$propertytype[0]->name}}
								@endif
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label><b>Flags</b></label>
							<div>
								@if(count($flags)>0)
								{{$flags[0]->flag_name}}
								@endif
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label><b>Property Type</b></label>
							<div>
								@if(count($category)>0)
								{{$category[0]->category_name}}
								@endif
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label><b>Description</b></label>
							<div>
								{!! $property->description !!}
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label><b>Status</b></label>
							<div>
								@if($property->status == 1)
								<input type="checkbox" id="status" name="status" checked disabled>
								@else
								<input type="checkbox" id="status" name="status" disabled>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							@if($property->image == '')
							<img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" />
							@else
							<img src="{{ asset('assets/images/property/'.$property->image) }}" class="img-responsive" alt="properties" />
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection('main_content')