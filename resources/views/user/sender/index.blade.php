@extends('layouts.dashboard.app')
@section('body')
{{-- <style>
    [x-cloak] { display: none !important; }
    @media (min-width: 1024px) {
        .sidebar-large-screen { display: block !important; }
    }
</style> --}}
<div class="flex flex-col h-screen bg-[rgb(242,243,244)]">
    <div x-data="{ open: false , menuBarOpen:true, isSmallScreen: window.innerWidth <= 640 }" @resize.window="isSmallScreen = window.innerWidth <= 640; if(isSmallScreen) menuBarOpen = false" x-init="() => { if(isSmallScreen) menuBarOpen = false }">

        @livewire('sender.header.header')

        <div class="flex flex-col lg:flex-row relative">
            <!-- Overlay -->
            <div :class="{ 'fixed inset-0 z-40 bg-gray-800 opacity-50 transition-opacity duration-0 ease-in-out': isSmallScreen }"
                x-show="menuBarOpen && isSmallScreen" x-cloak x-transition:enter="ease-out duration-0" x-transition:leave="ease-in duration-0"
                @click="menuBarOpen = false">
            </div>
            <!-- Sidebar -->
            <aside id="logo-sidebar" x-show="menuBarOpen" x-cloak x-transition:enter="ease-out duration-500"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="ease-in duration-500" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed top-0 left-0 w-64 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                 :class="{ 'z-50 h-screen w-64': isSmallScreen, 'w-64': !isSmallScreen }">
                <livewire:sender.sidebar.sidebar :sidenav="Session::get('panel')['feature'] ?? null" />
            </aside>

            <!-- Main Content -->
            <div id="dynamic-view" class="flex-grow overflow-y-auto" :class="{ 'z-30': isSmallScreen }">
                <div class="flex-1 sm:ml-64" id="dashboard-body" :class="{ 'sm:ml-64': menuBarOpen, 'sm:ml-0': !menuBarOpen }">
                    @livewire('sender.screens.screen', ['features' => Session::get('panel')['feature'] ?? null])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
