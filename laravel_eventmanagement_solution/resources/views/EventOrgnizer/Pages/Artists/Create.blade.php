@extends('EventOrgnizer/Layouts/Layout/HomeLayout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create Artist</h4>
                    <a href="{{ route('ArtistsOrganizer') }}" class="btn rounded-pill btn-primary">Back</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('StoreArtistOrganizer') }}" method="POST" id="regForm" name="formPrevent" enctype="multipart/form-data">
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
                                    <!-- <span class="input-group-text" id="video">Videos</span> -->
                                </div>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Profile Image <span class="required">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="file" name="image" id="image" class="form-control" aria-describedby="image" />
                                    <!-- <span class="input-group-text" id="image">Image</span> -->
                                </div>
                            </div>
                            <!-- <div class="mb-3 col-sm-4">
                                <label class="form-label" for="image">Artist Images(Gallery)</label>
                                <div class="input-group input-group-merge">
                                    <input type="file" id="galaey_image" name="gallary_images[]" class="form-control" aria-describedby="image" multiple />
                                    <span class="input-group-text" id="gallary_image">Image</span>
                                </div>
                            </div> -->
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


<script>
    $(document).ready(function() {
        $("#regForm").validate({
            rules: {
                name: "required",
                email: "required",
                contact_number: {
                    required: true,
                    number: "Enter Number only",
                },
                image: "required",
                end_date: "required",
                start_time: "required",
                end_time: "required",
                duration: "required",
                category_id: "required",
                'artist[]': "required",
                description: "required",
                'ticket_cat[]': "required",
                'ticket_qty[]': "required",
                'ticket_price[]': "required",
                foodstore: "required",
                about: "required",
            },
        });
    });
</script>

@endsection('content')