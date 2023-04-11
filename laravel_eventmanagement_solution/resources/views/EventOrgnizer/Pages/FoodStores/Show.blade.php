@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
	<div class="row">
		<div class="col-xl">
			<div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-datas-center">
					<h4>	Food Store Details</h4>
					<div class="col col-md-6">
						<a href="{{ route('FoodStoresOrganizer') }}" class="btn btn-primary btn-sm float-end">Back</a>
					</div>
				</div>
				<div class="card-body mt-3">
					<div class="row">
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>Name</b></label>
							<div>
								{{ $data->name }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>Email</b></label>
							<div>
								{{ $data->email }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>Contact Number</b></label>
							<div>
								{{ $data->contact_number }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>Address</b></label>
							<div>
								{{ $data->address }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>City</b></label>
							<div>
								{{ $data->city }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>State</b></label>
							<div>
								{{ $data->state }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>Country</b></label>
							<div>
								{{ $data->country }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>Postal Code</b></label>
							<div>
								{{ $data->postal_code }}
							</div>
						</div>
						<div class="col-sm-3 mb-3">
							<label class=" col-label-form"><b>Store Image</b></label>
							<div>
								<img src="{{ asset('Assets/images/' . $data->image) }}" style="height:120px; width:200px" />
							</div>
						</div>
						@if(isset($data->video)==1)
						<div class="col-sm-3 mb-3">
							<label class="col-label-form"><b>Store Video</b></label>
							<div>
								<embed src="{{ asset('Assets/images/' . $data->video) }}" style="height:120px; width:200px" />
							</div>
						</div>
						@endif
						<div class="col-sm-12 mb-3">
							<label class=" col-label-form"><b>Description</b></label>
							<div>
								{!!$data->description!!}
							</div>
						</div>
						@if(is_array(json_decode($data->gallary_images)))
						<label for="" class="col-label-form"><b>Gallery</b></label><br>
						<?php foreach (json_decode($data->gallary_images) as $pictures) { ?>
							<div class="col-sm-3 mb-3">
								<img class="single-gallery-image" src="{{asset('Assets/images/'.$pictures) }}" style="height:150px; width: 150px" />
							</div>
						<?php } ?>
						@endif
					</div>
					<div class="row">
						<div class="col-sm-3  mb-3">
							<label class="col-label-form"><b>Status</b></label>
							<div>
								@if($data->status ==1)
								<span class="badge bg-label-primary me-1">ACTIVE</span>
								@else
								<span class="badge bg-label-warning me-1">INACTIVE </span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md mb-4 mb-md-0">
							<small class="text-light fw-semibold">Basic Accordion</small>
							<div class="accordion mt-3" id="accordionExample">
								<div class="card accordion-item ">
									<h2 class="accordion-header" id="headingOne">
										<button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="false" aria-controls="accordionOne">
											Accordion Item 1
										</button>
									</h2>

									<div id="accordionOne" class="accordion-collapse collapsed show" data-bs-parent="#accordionExample">
										<div class="accordion-body">
											<div class="row">
												<h3><b>Food Menu</b></h3>
											@foreach($food_menu as $index=>$menu)
												<div class="col-sm-2 mb-2">
													<label for="" class="col-label-form"><b>Food Name</b></label>
													<div> <span><b>{{$index+1}}.</b></span>
														{{ $menu->name }}
													</div>
												</div>
												<div class="col-sm-2 mb-2">
													<label for="" class="col-label-form"><b>Food Price</b></label>
													<div>
														{{ $menu->price }}
													</div>
												</div>
												<div class="col-sm-3 mb-3">
													<label for="" class="col-label-form"><b>Food Image</b></label>
													<div>
														<img src="{{asset('Assets/images/' .$menu->image)}}" alt="" width="150" height="100">
													</div>
												</div>
												<div class="col-sm-3 mb-3">
													<label for="" class="col-label-form"><b>Food Video</b></label>
													<div>
														<embed src="{{ asset('Assets/images/' . $menu->video) }}" style="height:100px; width:150px" />
													</div>
												</div>
												<div class="col-sm-2 mb-3">
													<label for="" class="col-label-form"><b>Status</b></label>
													<div>
														@if($menu->status == 1)
														<span class="badge bg-label-primary me-1">ACTIVE</span>
														@else
														<span class="badge bg-label-warning me-1">INACTIVE</span>
														@endif
													</div>
												</div>
												<div class="col-sm-12 mb-3">
													<label for="" class="col-label-form"><b>Food Description</b></label>
													<div>
														{!! $menu->description !!}
													</div>
												</div>
												<br>
												<br>
												@endforeach
												
											</div>
										</div>
									</div>
								</div>


							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
	</div>
</div>
</div>
@endsection('content')