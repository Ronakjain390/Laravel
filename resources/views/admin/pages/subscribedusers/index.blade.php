@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="page-header">
                    <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                            <i class="mdi mdi-account-star"></i>
                        </span> Subscribed Users
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="datatable-crud">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Email</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#datatable-crud').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('/admin/subscribed-users') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'email',
                    name: 'email',
                    searchable: true
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    searchable: true
                }
            ],
            order: [
                [0, 'asc']
            ]
        });
    });
</script>

@endsection('main_content')