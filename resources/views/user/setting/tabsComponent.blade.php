{{-- @extends('layouts.dashboard.app')
@section('body')
 
    <div class="ml-64">

        @livewire('setting.screens.tabcomponent')

    </div>
@endsection --}}
@extends('layouts.dashboard.app')
@section('body')


<div id="dynamic-view">
    {{-- @dd('jdskf'); --}}
    @livewire('setting.screens.tabs-component', ['features' => Session::get('panel')])

</div>
@endsection