@extends('layouts.dashboard.app')
@section('body')
<style>
     @media only screen and (min-width: 601px) {
        #logo-sidebar {
            display: block;
        }
    }
</style>
<div x-data="{ open: true }">
    @livewire('dashboard.header.header')


    <div class="">
        <!-- Dark overlay for the background -->
        <div class="fixed inset-y-0 left-0 z-50 bg-gray-800 opacity-0 transition-opacity duration-0 ease-in-out"
            x-show="open" x-cloak x-transition:enter="ease-out duration-0" x-transition:leave="ease-in duration-0">
        </div>


        <aside id="logo-sidebar"   x-show="menuBarOpen" x-cloak x-transition:enter="ease-out duration-500"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="ease-in duration-500" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed top-0 left-0 w-64 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                 :class="{ 'z-50 h-screen w-64': isSmallScreen, 'w-64': !isSmallScreen }">
            @livewire('setting.sidebar.sidebar')
        </aside>

        <div id="dynamic-view">
            <div class="">
                <div class="p-4  sm:ml-64 " id="dashboard-body" :class="{ 'sm:ml-64': menuBarOpen, 'sm:ml-0': !menuBarOpen }">
                    @livewire('setting.team-member', ['features' => Session::get('panel')])
                </div>
            </div>
        </div>
        <script>
            function initSidebar() {
            window.addEventListener('toggleSidebar', () => {
                this.open = !this.open;
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            initSidebar();
        });
        </script>
    </div>
</div>
@endsection
