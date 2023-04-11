<form class="filter-form" method="get" action="{{ route('search-perameter') }}">

    <div class="modal-body">
        <div class="row filterRow">
            <div class="col-sm-12 col-md-12">
                <label for="ptype">Property Type</label>
                <select class="form-control " name="ptype" id="ptype">
                    <?php $ptype = ptype() ?>
                    @foreach($ptype as $protype)
                    @if( app('request')->input('ptype') == $protype->id)
                    <option selected value="{{ $protype->id }}">{{$protype->name}}</option>
                    @else
                    <option value="{{ $protype->id }}">{{$protype->name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div><br>
        <div class="row filterRow">
            <div class="col-md-12 col-sm-12">
                <header><b>Price</b></header>
            </div>
            <div class="col-md-12 priceRange">
                <?php $Currency = Currency() ?>
                {{$Currency[0]->currency_symbol}} <span id="value"></span>
            </div>
            <div class="col-md-3 col-sm-1">
                <b>{{$Currency[0]->currency_symbol}} 0</b>
            </div>
            <div class="col-md-6 col-sm-9">
                <input type="range" name="price" id="price" min="0" max="10000000" step=10 value="{{app('request')->input('price')}}">
            </div>
            <div class="col-md-3 col-sm-3">
                <b>{{$Currency[0]->currency_symbol}} 1Cr+</b>
            </div>
        </div><br>
        <div class="row filterRow">
            <div class="col-md-12 col-sm-12">
                <header><b>Select Bedroom</b></header>
            </div>
            <div class="col-md-6 col-sm-6">
                <label for="agent"></label>
                <select class="form-control " name="minbedroom" id="minbedroom">
                    @if(app('request')->input('minbedroom'))
                    <option selected value="{{app('request')->input('minbedroom')}}">{{app('request')->input('minbedroom')}}</option>
                    @else
                    <option selected>Min</option>
                    @endif
                    <option value="2">2</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                </select>
            </div>
            <div class="col-md-6 col-sm-6">
                <label for="agent"></label>
                <select class="form-control" name="maxbedroom" id="maxbedroom">
                    @if(app('request')->input('maxbedroom'))
                    <option selected value="{{app('request')->input('maxbedroom')}}">{{app('request')->input('maxbedroom')}}</option>
                    @else
                    <option selected>Max</option>
                    @endif
                    <option value="2">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                </select>
            </div>
        </div><br>
        <div class="row filterRow">
            <div class="col-md-12 col-sm-6">
                <label for="agent">No. of Bathroom</label>
                <select class="form-control " name="bathroom" id="bathroom">
                    <option selected value="{{app('request')->input('bathroom')}}">{{app('request')->input('bathroom')}}</option>
                    <option value="2">2</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                </select>
            </div>
        </div><br>
        <div class="row filterRow">
            <div class="col-sm-12 col-md-12">
                <label for="amenities">Additional Facilities</label>
                <?php $aminities = aminities() ?>
                <select class="form-control " name="amenities" id="amenities">
                    <option>--Select Ameneties--</option>
                    @foreach($aminities as $aminities)
                    <option value="{{ $aminities->id }}">{{ $aminities->amenities }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" id="savebutton" onclick="closeModel()" class="btn btn-primary">Search</button>
    </div>
</form>
<script>
    const value = document.querySelector("#value")
    const input = document.querySelector("#price")
    value.textContent = input.value
    input.addEventListener("input", (event) => {
        value.textContent = event.target.value
    })
</script>