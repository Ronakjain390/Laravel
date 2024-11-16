@extends('layouts.admin.dashboard.app')
@section('body')
@livewire('admin.dashboard.header.header')

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen  transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
    @livewire('admin.dashboard.sidebar.sidebar')

</aside>

<div class="sm:ml-64 h-screen" id="dashboard-body">
    @livewire('admin.dashboard.coupons')
</div>
@endsection
