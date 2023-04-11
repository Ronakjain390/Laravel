@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
  @if ($message = Session::get('success'))
  <div class="alert alert-success">
    <p>{{ $message }}</p>
  </div>
  @endif
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-home"></i>
      </span> Dashboard
    </h3>
  </div>
  <div class="row">
    <div class="col-md-4 stretch-card grid-margin">
      <div class="card bg-gradient-info card-img-holder text-white">
        <div class="card-body">
          <a href="{{ url('/admin/users') }}" class="text-decoration-none ">
            <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3 text-white"><strong>{{ $users }}</strong> Users <i class="mdi mdi-account mdi-24px float-right"></i>
            </h4>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
      <div class="card bg-gradient-dark card-img-holder text-white">
        <div class="card-body">
          <a href="{{ url('/admin/agents') }}" class="text-decoration-none ">
            <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3 text-white"><strong>{{ $agents }}</strong> Agents <i class="mdi mdi-account-box-multiple mdi-24px float-right"></i>
            </h4>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
      <div class="card bg-gradient-danger card-img-holder text-white">
        <div class="card-body">
          <a href="{{ url('/admin/banners') }}" class="text-decoration-none ">
            <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3 text-white"><strong>{{ $banners }}</strong> Banner <i class="mdi mdi-flag-plus mdi-24px float-right"></i>
            </h4>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
      <div class="card bg-gradient-success card-img-holder text-white">
        <div class="card-body">
          <a href="{{ url('/admin/templates') }}" class="text-decoration-none ">
            <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3 text-white"><strong>{{ $templates }}</strong> Templates <i class="mdi mdi-checkbox-multiple-blank mdi-24px float-right"></i>
            </h4>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
      <div class="card bg-gradient-primary card-img-holder text-white">
        <div class="card-body">
          <a href="{{ url('/admin/news') }}" class="text-decoration-none ">
            <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3 text-white"><strong>{{ $news }}</strong> News <i class="mdi mdi-newspaper mdi-24px float-right"></i>
            </h4>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
      <div class="card bg-gradient-info card-img-holder text-white">
        <div class="card-body">
          <a href="{{ url('/admin/reviewsratings') }}" class="text-decoration-none ">
            <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3 text-white"><strong>{{ $reviews }}</strong> Reviews <i class="mdi mdi-more menu-icon mdi-24px float-right"></i>
            </h4>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection