@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Artist Details</h5>
                    <a href="{{ route('Artists') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateArtist',$data->id) }}" method="POST" enctype="multipart/form-data" name="formPrevent">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="name">Name </label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ $data->name }}" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="name">Nick Name </label>
                                <input type="text" class="form-control" name="nick_name" placeholder="Name" value="{{ $data->nick_name }}" />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label" for="phoneNumber">Phone Number</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">(+91)</span>
                                    <input type="text" id="phoneNumber" name="contact_number" value="{{$data->contact_number }}" class="form-control" placeholder="202 555 0111" />
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="bio">Bio </label>
                                <input type="text" class="form-control" name="bio" id="bio" placeholder="Bio" value="{{ $data->bio }}" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-email">Email </label>
                                <div class="input-group input-group-merge">
                                    <input type="text" id="basic-default-email" value="{{ $data->email }}" class="form-control" placeholder="Email" name="email" aria-label="john.doe" aria-describedby="basic-default-email2" />
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="country">Country </label>
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country" value="{{ $data->country }}" />
                            </div>
                            <div class="col-sm-4 form-group">
                                <label for="reason">Artist's Expertize </label>
                                <select class="form-control" name="expertize" id="expertize" multiple>
                                    @foreach($art_expertize as $item)
                                    <option selected value="{{ $item->id }}">{{$item->name}}</option>
                                    @endforeach
                                    @foreach($exp as $exp1)
                                    <option value="{{$exp1->id}}">{{ $exp1->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-image">Profile Image</label>
                                <input type="file" class="form-control" name="image" />
                                <img src="{{ asset('Assets/images/' . $data->image) }}" width="100" class="img-thumbnail" />
                                <input type="hidden" name="hidden_image" value="{{ $data->image }}" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-image">Artist Gallery</label>
                                <input type="file" class="form-control" id="image" name="gallary_images[]" multiple />
                                @if(is_array(json_decode($data->gallary_images)))
                                <?php $a = json_decode($data->gallary_images); ?>
                                <div class="row">
                                    <?php for ($i = 0; $i < count($a); $i++) { ?>
                                        <div class="col-sm-3">
                                            <img src="{{asset('Assets/images/'.$a[$i]) }}" width="100" class="img-thumbnail" />

                                            <input type="hidden" name="hidden_Image" value="" />
                                        </div>
                                    <?php } ?>
                                </div>
                                @endif
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="storevideo">Store Videos </label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="video" name="video" class="form-control" aria-describedby="video" />
                                    <span class="input-group-text" id="video">Videos</span>
                                </div>
                                <embed src="{{ asset('Assets/images/' . $data->video) }}" style="height:100px; width:150px" />

                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" placeholder="Status" {{ $data->status=='1' ? 'checked':'' }} />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminContent')