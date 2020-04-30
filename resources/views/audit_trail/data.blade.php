@extends('layouts.master')
@section('title')
    {{ trans_choice('general.audit_trail',2) }}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">  {{ trans_choice('general.audit_trail',2) }} </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th> {{ trans_choice('general.user',1) }}</th>
                        <th> {{ trans_choice('general.note',2) }}</th>
                        <th> {{ trans_choice('general.date',2) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                    {{$key->user}}

                            </td>
                            <td>{{ $key->notes }}</td>
                            <td>{{ $key->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

    <script>
        $('#data-table').DataTable({
            "order": [[2, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
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
                }
            },
            responsive: false
        });
    </script>
@endsection
