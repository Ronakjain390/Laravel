@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
              <i class="mdi mdi-account"></i>
            </span> Enquiries
          </h3>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered" id="datatable-crud">
          <thead>
            <tr>
              <th>S.No</th>
              <th>Name</th>
              <th>Email</th>
              <th>Contact</th>
              <th>Description</th>
              <th>Created at</th>
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
      ajax: "{{ url('/admin/propertyenquiries') }}",
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
        },
        {
          data: 'name',
          name: 'name',
          searchable: true
        },
        {
          data: 'email',
          name: 'email',
          searchable: true
        },
        {
          data: 'contact',
          name: 'contact',
          searchable: true
        },
        {
          data: 'description',
          name: 'description',
          searchable: true
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
      ],
      order: [
        [0, 'desc']
      ]
    });

  });
</script>

@endsection('main_content')