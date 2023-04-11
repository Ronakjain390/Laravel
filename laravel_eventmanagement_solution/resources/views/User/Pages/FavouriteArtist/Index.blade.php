<div class="container">
    <div class="spacer blog">
        <div class="row">
            <div class="col-lg-8 col-sm-12 ">
                @if($favouriteArtist == '')
                <div class="row">
                    There is no Data.
                </div>
                @else
                @foreach($favouriteArtist as $item)
                <!-- blog list -->
                <div class="row">
                    <div class="col-lg-4 col-sm-4 "><a href="{{ route('userPropertyShow', ['id' => $item->id]) }}" class="thumbnail">
                            @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)))
                            <img src="{{ asset('assets/images/property/'.$item->image) }}" style="height: 160px; width: 100%;" alt="img">
                            @else
                            <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" style="height: 160px; width: 100%;" alt="properties" />
                            @endif
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-3 ">
                        <h3><a href="{{ route('userPropertyShow', ['id' => $item->id]) }}">{{ $item->title }}</a></h3>
                        @if($item->description)
                        <p>{!! substr($item->description, 0, 150) !!} </p>
                        @endif
                    </div>
                    <div class="col-lg-3 col-sm-3">
                        <a href="{{ route('wishlistdelete',['id' => $item->id]) }}" class="btn btn-danger btnWishlist">
                            <i class='fa fa-heart'></i>
                        </a>
                    </div>
                </div>
                <!-- blog list -->
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
