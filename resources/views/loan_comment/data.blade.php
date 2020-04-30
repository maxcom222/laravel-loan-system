@extends('layouts.master')
@section('title')Loan Comment
@endsection
@section('current-page')Loan Comment
@endsection
@section('content')
        <!-- Default box -->
<div class="box">
    <div class="box-header">
        <h6 class="panel-title">Loan Status</h6>

        <div class="heading-elements">
            <a href="{{ url('loan/loan_status/create') }}"
               class="btn btn-info btn-sm">Add Loan Status</a>
        </div>
    </div>
    <div class="panel-body">
        <table id="" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Name</th>
                <th>{{ trans('general.action') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $key)
                <tr>
                    <td>{{ $key->name }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                    data-toggle="dropdown" aria-expanded="false">
                                {{ trans('general.choose') }} <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('loan/loan_status/'.$key->id.'/edit') }}"><i
                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                <li><a href="{{ url('loan/loan_status/'.$key->id.'/delete') }}"
                                       data-toggle="confirmation"><i
                                                class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.box -->
@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $('#data-table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                },
                "columnDefs": [
                    {"orderable": false, "targets": 2}
                ]
            },
        });
    </script>
@endsection
