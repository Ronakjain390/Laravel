@extends('layouts.admin.dashboard.app')
@section('body')
@livewire('admin.dashboard.header.header')

<aside id="logo-sidebar" class="fixed top-0 left-0  w-64 h-screen  transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
    @livewire('admin.dashboard.sidebar.sidebar')

</aside>

<div class="p-4  sm:ml-64 h-screen" id="dashboard-body">
    @livewire('admin.dashboard.allusers')
</div>
@endsection