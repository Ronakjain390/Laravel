@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
	<div class="row">
		<div class="col-xl">
			<div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h4 class="mb-0"> Artist Details</h4>
					<div class="col col-md-6">
						<a href="{{ route('ArtistsOrganizer') }}" class="btn btn-primary btn-sm float-end">Back</a>
					</div>
				</div>
				<div class="card-body mt-3">
					<div class="row">
						<div class="mb-3 col-sm-3">
							<label class="col-label-form"><b>Artist Name</b></label>
							<div>
								{{ $item->name }}
							</div>
						</div>

						<div class="mb-3 col-sm-3">
							<label class=" col-label-form"><b>Email</b></label>
							<div>
								{{$item->email}}
							</div>
						</div>
						<div class="mb-3 col-sm-3">
							<label class=" col-label-form"><b>Contact Number</b></label>
							<div>
								{{$item->contact_number}}
							</div>
						</div>
						<div class="mb-3 col-sm-3">
							<label class=" col-label-form"><b>Country</b></label>
							<div>
								{{$item->country}}
							</div>
						</div>
						<div class="col-sm-3">
							<label class=" col-label-form"><b>Artist Images</b></label>
							<div>
								@if($item->image == '')
								<img src="{{ asset('Assets/DefaultImage/defaultProfile.png') }}" class="img-thumbnail" alt="No Image" style="height:120px; width:200px" />
								@else
								<img src="{{ asset('Assets/images/' . $item->image) }}" class="img-thumbnail" alt="no Image" style="height:120px; width:200px" />
								@endif
							</div>
						</div>
						<!-- @if(isset($item->video)==1)
						<div class="col-sm-3">
							<label class=" col-label-form"><b>Artist Videos</b></label>
							<div>
								<embed src="{{ asset('Assets/images/' . $item->video) }}" style="height:120px; width:200px" />
							</div>
						</div>
						@endif -->
						<div class="col-sm-3">
							<label class="col-label-form"><b>Status</b></label>
							<div>
								@if($item->status ==1)
								<span class="badge bg-label-primary me-1">ACTIVE</span>
								@else
								<span class="badge bg-label-warning me-1">INACTIVE</span>
								@endif
							</div>
						</div>

						<div class=" mt-4 col-sm-12">
							<label class=" col-label-form"><b>Bio</b></label>
							<div>
								{!!$item->bio!!}
							</div>
						</div>
						<div class="col-sm-12 mt-4">
							@if(count($exp1)>0)
							<label class=" col-label-form"><b>Expertizes</b></label>
							@foreach ($exp1 as $index=>$exp)
							<div class="mb-2 col-sm-3">
								<div>
									<span><b>{{$index+1}}.</b></span>{{$exp->name}}
								</div>
							</div>
							@endforeach
							@endif
						</div>
						@if(is_array(json_decode($item->gallary_images)))
						<label class=" col-label-form"><b>Gallery</b></label>
						@foreach(json_decode($item->gallary_images) as $gal)
						<div class="col-sm-4 mt-4">
							<img src="{{ asset('Assets/images/'.$gal) }}" alt="gal_img" width="150" height="100">
						</div>
						@endforeach
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@endsection('content')