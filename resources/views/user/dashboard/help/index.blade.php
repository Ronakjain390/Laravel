@extends('layouts.dashboard.app')
@section('body')
@livewire('dashboard.header.header')

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen  transition-transform -translate-x-full bg-[#f3f4f6] border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
    @livewire('dashboard.sidebar.sidebar')
</aside>

<div class="p-4  sm:ml-64 h-auto  bg-[#f3f4f6] ">

    @livewire('dashboard.help')
</div>
@endsection
