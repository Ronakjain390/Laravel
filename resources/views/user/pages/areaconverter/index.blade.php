@extends('user/layouts/parts/master')
@section('content')
<!-- banner -->
<div >
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
<div class="formstart" id="app">
    <div class="container">
        <div class="spacer">
            <div class="row area-converter">
                <div class="col-md-6 col-sm-8 col-12 form">
                    <form>
                        <div class="row mt-2">
                            <h3 class="page-title text-center">Area Calculator</h3>
                            <div class="col-md-6 form-group">
                                <label for="acre">Acre:</label>
                                <input type="text" class="form-control" name="acre" @input="area('acre', acre)" v-model="acre" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="hectare">Hectare:</label>
                                <input type="email" class="form-control" name="hectare" @input="area('hectare', hectare)" v-model="hectare" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="square_foot">Square foot:</label>
                                <input type="text" class="form-control" name="square_foot" @input="area('square_foot', square_foot)" v-model="square_foot" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="square_inch">Square Inch</label>
                                <input type="text" class="form-control" name="square_inch" @input="area('square_inch', square_inch)" v-model="square_inch" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="square_km">Square Km</label>
                                <input type="text" class="form-control" name="square_km" @input="area('square_km', square_km)" v-model="square_km" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="square_meter">Square Meter</label>
                                <input type="text" class="form-control" name="square_meter" @input="area('square_meter', square_meter)" v-model="square_meter" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="square_mile">Square Mile</label>
                                <input type="text" class="form-control" name="square_mile" @input="area('square_mile', square_mile)" v-model="square_mile" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="square_yard">Square Yard</label>
                                <input type="text" class="form-control" name="square_yard" @input="area('square_yard', square_yard)" v-model="square_yard" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection('content')