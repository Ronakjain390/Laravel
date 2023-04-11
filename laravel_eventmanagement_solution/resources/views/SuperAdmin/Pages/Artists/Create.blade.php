@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                @if (Session::has('success'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Artist</h5>
                    <a href="{{ route('Artists') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('StoreArtist') }}" method="POST" id="regForm" name="formPrevent" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="name">Artist Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="name">Nick Name </label>
                                <input type="text" class="form-control" name="nick_name" id="nick_name" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-email">Email <span class="required">*</span></label>
                                <input type="text" id="basic-default-email" class="form-control" placeholder="Email" name="email" aria-label="john.doe" aria-describedby="basic-default-email2" />

                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-phone">Contact Number <span class="required">*</span></label>
                                <input type="text" id="basic-default-phone" class="form-control phone-mask" name="contact_number" placeholder="Contact Number" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="bio">Bio <span class="required">*</span></label>
                                <input type="text" class="form-control" name="bio" id="bio" placeholder="Bio" />
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="country">Country <span class="required">*</span></label>
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country" />
                            </div>

                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="itemvideo">Artist Videos</label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="video" name="video" class="form-control" aria-describedby="video" multiple />
                                    <span class="input-group-text" id="video">Videos</span>
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Profile Image <span class="required">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="file" name="image" id="image" class="form-control" aria-describedby="image" />
                                    <span class="input-group-text" id="image">Image</span>
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Artist Images(Gallery)</label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="galaey_image" name="gallary_images[]" class="form-control" aria-describedby="image" multiple />
                                    <span class="input-group-text" id="gallary_image">Image</span>
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4 expertize-group">
                                <label for="expertize">Artist's Expertize</label><br>
                                <select class="form-control expertize" name="expertize[]" id="expertize" multiple>
                                    @foreach ($data as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="basic-default-postal_code">Status</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" checked />
                                    <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    $(document).ready(function() {
        $("#regForm").validate({
            rules: {
                name: "required",
                email: "required",
                contact_number: "required",
                'expertize[]': "required",
                bio: "required",
                country: "required",
                image: {
                    inputimage: {
                    required: true,
                    accept: "image/jpeg, image/png, image/gif",
                    filesize: 500
                }
            }
                
            },
            messages: {
                image: {
                    required: "Please select an image file.",
                    accept: "Invalid file type. Please select a JPEG, PNG or GIF file.",
                    filesize: "File size exceeds the limit of 1 MB. Please select a smaller file."
                }
            },
            errorPlacement: function(error, element) {
                $(error).addClass('text-danger')
                if ($(element).hasClass('expertize')) {
                    $(element).closest('.expertize-group').parent().append(error);
                } else {
                    element.after(error);
                }
            },
        });
    });
</script>
@endpush
@endsection('SuperAdminContent')