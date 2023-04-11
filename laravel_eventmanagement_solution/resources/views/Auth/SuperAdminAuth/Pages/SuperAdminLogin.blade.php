@extends('Auth/SuperAdminAuth/Layouts/Layout/Base')
@section('SuperAdminAuthContent')
<div class="row">
    <div class="col-lg-6 col-md-8 login-box">
        <div class="col-lg-12 login-title">
            ADMIN PANEL
        </div>

        <div class="col-lg-12 login-form">
            <div class="col-lg-12 login-form">
                <form autocomplete="off">
                    <div class="form-group">
                        <label class="form-control">USERNAME</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-control">PASSWORD</label>
                        <input type="password" class="form-control" i>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">LOGIN</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection('SuperAdminAuthContent')