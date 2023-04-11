@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <!-- model value  -->
            <div id="myModel" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Record ?</h5>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this record?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="deleteButton" onclick="closeModel()" class="btn btn-secondary">Close</button>
                            <button type="submit" id="savebutton" onclick="closeModel()" class="btn btn-primary rounded-1">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- model value  -->
            <div class="row">
                <div class="page-header">
                    <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                            <i class="mdi mdi-newspaper"></i>
                        </span> Flags
                    </h3>
                    <nav aria-label="breadcrumb">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">
                                <span></span> <a href="{{ route('flagcreate') }}" class="btn btn-success btn-lg float-right">Create</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            @if (Session::has('success'))

            <div class="alert alert-success">
                <p>{{ Session::get('success') }}</p>
            </div>
            @endif
            <div class="card-body">
                <table class="table table-bordered" id="datatable-crud">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Flag Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->flag_name}}</td>
                            <td><a href="{{ route('flagdestroy', ['id' => $data->id]) }}"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    let nikh

    function togglemodel(id) {
        nikh = id
        let mod = document.getElementById('myModel').style.display = "block"
    }

    function closeModel() {
        let clos = document.getElementById('myModel').style.display = "none"
    }
</script>
@endsection('main_content')