@extends('layouts.master')
@section('title')
    {{ trans_choice('general.user',2) }}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.user',2) }}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('users.create'))
                    <a href="{{ url('user/create') }}" class="btn btn-info btn-xs">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.user',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table  table-striped table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans('general.name') }}</th>
                    <th>{{ trans('general.gender') }}</th>
                    <th>{{ trans('general.phone') }}</th>
                    <th>{{ trans_choice('general.email',1) }}</th>
                    <th>{{ trans('general.address') }}</th>
                    <th>{{ trans_choice('general.role',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->first_name }} {{ $key->last_name }}</td>
                        <td>{{ $key->gender }}</td>
                        <td>{{ $key->phone }}</td>
                        <td>{{ $key->email }}</td>
                        <td>{!!   $key->address !!}</td>
                        <td>
                            @if(!empty($key->roles))
                                @if(!empty( $key->roles->first()))
                                    <span class="label label-danger">{{ $key->roles->first()->name }} </span>
                                @endif
                            @endif
                        </td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @if(Sentinel::hasAccess('users.view'))
                                            <li>
                                                <a href="{{ url('user/'.$key->id.'/show') }}"><i
                                                            class="fa fa-search"></i>
                                                    {{ trans_choice('general.detail',2) }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('users.update'))
                                            <li>
                                                <a href="{{ url('user/'.$key->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i>
                                                    {{ trans('general.edit') }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('users.delete'))
                                            <li>
                                                <a href="{{ url('user/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i>
                                                    {{ trans('general.delete') }}</a>
                                            </li>
                                        @endif
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
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [6]}
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
