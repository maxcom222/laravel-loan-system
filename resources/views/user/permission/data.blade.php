@extends('layouts.master')
@section('title')
    {{trans_choice('general.permission',2)}}
@endsection
@section('content')

    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.permission',2)}}</h6>

            <div class="heading-elements">
                <a href="{{ url('user/permission/create') }}" class="btn btn-info btn-xs">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table   table-hover table-striped" id="">
                <thead>
                <tr>
                    <th>{{trans('general.name')}}</th>
                    <th>{{trans('general.parent')}}</th>
                    <th>{{trans('general.slug')}}</th>
                    <th>{{trans('general.action')}}</th>
                </tr>
                </thead>

                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>
                            @if($key->parent_id!=0)
                                |___
                            @endif
                            {{ $key->name }}
                        </td>
                        <td>
                            @if(count($key->parent)>0)
                                {{ $key->parent->name }}
                            @else
                                {{trans('general.no_parent')}}
                            @endif
                        </td>
                        <td>{{ $key->slug}}</td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="{{ url('user/permission/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i>
                                                {{ trans('general.edit') }}</a>
                                        </li>

                                        <li>
                                            <a href="{{ url('user/permission/'.$key->id.'/delete') }}"
                                               data-toggle="confirmation"><i
                                                        class="fa fa-trash"></i>
                                                {{ trans('general.delete') }}</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')

    <script>
        $('#data-table').DataTable({
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
                    {"orderable": false, "targets": 0}
                ]
            },
            responsive: true,
        });
    </script>
@endsection
