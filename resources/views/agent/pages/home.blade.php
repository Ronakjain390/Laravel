@extends('agent/layouts/master')
@section('content')
<div class="app-content pt-3 p-md-3 p-lg-4">
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <p class="m-0">{{ $message }}</p>
    </div>
    @endif
    <div class="container-xl">

        <h1 class="app-page-title">Dashboard</h1>


        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                    <div class="app-card-body p-3 p-lg-4">
                        <h4 class="stats-type mb-1">Total Properties for Rent</h4>
                        <div class="stats-figure">{{ $agentrent}}</div>

                    </div>
                    <!--//app-card-body-->
                    <a class="app-card-link-mask"></a>
                </div>
                <!--//app-card-->
            </div>
            <!--//col-->

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                    <div class="app-card-body p-3 p-lg-4">
                        <h4 class="stats-type mb-1">Total Properties for Sell</h4>
                        <div class="stats-figure">{{ $agentsell}}</div>
                    </div>
                    <!--//app-card-body-->
                    <a class="app-card-link-mask"></a>
                </div>
                <!--//app-card-->
            </div>
            <!--//col-->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                    <div class="app-card-body p-3 p-lg-4">
                        <h4 class="stats-type mb-1">Properties for Enquiry</h4>
                        <div class="stats-figure">{{$enquiry}}</div>

                        <!--//app-card-body-->
                        <a class="app-card-link-mask"></a>
                    </div>
                    <!--//app-card-->
                </div>
                <!--//col-->

                <!--//col-->
            </div>
            <!--//row-->
            <div class="row g-4 p-0 mb-4 mx-auto">
                <div class="col-12 col-lg-6">
                    <div class="app-card app-card-chart h-100 shadow-sm">
                        <div class="app-card-header p-3">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-auto">
                                    <h4 class="app-card-title">Line Chart Example</h4>
                                </div>
                                <!--//col-->
                                <div class="col-auto">
                                    <div class="card-header-action">
                                        <a href="charts.html">More charts</a>
                                    </div>
                                    <!--//card-header-actions-->
                                </div>
                                <!--//col-->
                            </div>
                            <!--//row-->
                        </div>
                        <!--//app-card-header-->
                        <div class="chart-card app-card-body p-3 p-lg-4">
                            <div class="mb-3 d-flex">
                                <select class="form-select form-select-sm ms-auto d-inline-flex w-auto">
                                    <option value="1" selected>This week</option>
                                    <option value="2">Today</option>
                                    <option value="3">This Month</option>
                                    <option value="3">This Year</option>
                                </select>
                            </div>
                            <div class="chart-container">
                                <canvas id="canvas-linechart"></canvas>
                            </div>
                        </div>
                        <!--//app-card-body-->
                    </div>
                    <!--//app-card-->
                </div>
                <!--//col-->
                <div class="col-12 col-lg-6">
                    <div class="app-card app-card-chart h-100 shadow-sm">
                        <div class="app-card-header p-3">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-auto">
                                    <h4 class="app-card-title">Bar Chart Example</h4>
                                </div>
                                <!--//col-->
                                <div class="col-auto">
                                    <div class="card-header-action">
                                        <a href="charts.html">More charts</a>
                                    </div>
                                    <!--//card-header-actions-->
                                </div>
                                <!--//col-->
                            </div>
                            <!--//row-->
                        </div>
                        <!--//app-card-header-->
                        <div class="chart-card app-card-body p-3 p-lg-4">
                            <div class="mb-3 d-flex">
                                <select class="form-select form-select-sm ms-auto d-inline-flex w-auto">
                                    <option value="1" selected>This week</option>
                                    <option value="2">Today</option>
                                    <option value="3">This Month</option>
                                    <option value="3">This Year</option>
                                </select>
                            </div>
                            <div class="chart-container">
                                <canvas id="canvas-barchart"></canvas>
                            </div>
                        </div>
                        <!--//app-card-body-->
                    </div>
                    <!--//app-card-->
                </div>
                <!--//col-->

            </div>
            <!--//row-->
            <div class="row g-4 mb-4">

                <!--//col-->

                <!--//col-->
            </div>
            <!--//row-->
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-4">

                    <!--//app-card-->
                </div>
                <!--//col-->
                <div class="col-12 col-lg-4">

                    <!--//app-card-->
                </div>
                <!--//col-->
                <div class="col-12 col-lg-4">
                    <!--//app-card-->
                </div>
                <!--//col-->
            </div>
            <!--//row-->

        </div>
        <!--//container-fluid-->
    </div>
    @endsection