@extends('layouts.master')
@section('title')
    {{trans_choice('general.borrower',2)}} {{trans_choice('general.pending',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.borrower',2)}} {{trans_choice('general.pending',1)}}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('borrowers.create'))
                    <a href="{{ url('borrower/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.borrower',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body ">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.id',1)}}</th>
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.gender',1)}}</th>
                        <th>{{trans_choice('general.mobile',1)}}</th>
                        <th>{{trans_choice('general.status',1)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->id }}</td>
                            <td>{{ $key->first_name }} {{ $key->last_name }}</td>
                            <td>
                                @if($key->gender=="Male")
                                    {{trans_choice('general.male',1)}}
                                @endif
                                @if($key->gender=="Female")
                                    {{trans_choice('general.female',1)}}
                                @endif
                            </td>
                            <td>{{ $key->mobile }}</td>
                            <td>
                                @if($key->active==1)
                                    <span class="label label-success">{{trans_choice('general.active',1)}}</span>
                                @endif
                                @if($key->active==0)
                                    <span class="label label-danger">{{trans_choice('general.pending',1)}}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            @if($key->active==0)
                                                @if(Sentinel::hasAccess('borrowers.approve'))
                                                    <li><a href="{{ url('borrower/'.$key->id.'/approve') }}"><i
                                                                    class="fa fa-check"></i> {{trans_choice('general.approve',1)}}
                                                        </a></li>
                                                @endif
                                            @endif
                                            @if($key->active==1)
                                                @if(Sentinel::hasAccess('borrowers.approve'))
                                                    <li><a href="{{ url('borrower/'.$key->id.'/decline') }}"><i
                                                                    class="fa fa-minus-circle"></i> {{trans_choice('general.decline',1)}}
                                                        </a></li>
                                                @endif
                                            @endif
                                            @if(Sentinel::hasAccess('borrowers.blacklist'))
                                                @if($key->blacklisted==1)
                                                    <li><a href="{{ url('borrower/'.$key->id.'/unblacklist') }}"
                                                           class="delete"><i
                                                                    class="fa fa-check"></i>{{trans_choice('general.undo',1)}} {{trans_choice('general.blacklist',1)}}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($key->blacklisted==0)
                                                    <li>
                                                        <a href="{{ url('borrower/'.$key->id.'/blacklist') }}"
                                                           class="delete"><i
                                                                    class="fa fa-minus-circle"></i> {{trans_choice('general.blacklist',1)}}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(Sentinel::hasAccess('borrowers.view'))
                                                <li><a href="{{ url('borrower/'.$key->id.'/show') }}"><i
                                                                class="fa fa-search"></i> {{trans_choice('general.detail',2)}}
                                                    </a></li>
                                            @endif
                                            @if(Sentinel::hasAccess('borrowers.update'))
                                                <li><a href="{{ url('borrower/'.$key->id.'/edit') }}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('borrowers.delete'))
                                                <li><a href="{{ url('borrower/'.$key->id.'/delete') }}"
                                                       class="delete"><i
                                                                class="fa fa-trash"></i> {{ trans('general.delete') }}
                                                    </a>
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
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

    <script>
        $('#data-table').DataTable({
            "order": [[0, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [5]}
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
