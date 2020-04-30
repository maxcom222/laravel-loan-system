@extends('layouts.master')
@section('title')
    {{trans_choice('general.saving',2)}} {{trans_choice('general.account',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.saving',2)}} {{trans_choice('general.account',2)}}</h6>
            <div class="heading-elements">
                @if(Sentinel::hasAccess('savings.create'))
                    <a href="{{ url('saving/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.account',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body ">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.account',1)}}</th>
                        <th>{{trans_choice('general.borrower',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.balance',1)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->id }}</td>
                            <td>
                                @if(!empty($key->borrower))
                                    {{ $key->borrower->title }} {{ $key->borrower->first_name }} {{ $key->borrower->last_name }}
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->savings_product))
                                    {{ $key->savings_product->name }}
                                @endif
                            </td>
                            <td>{{ number_format(\App\Helpers\GeneralHelper::savings_account_balance($key->id),2) }}</td>
                            <td>
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @if(Sentinel::hasAccess('savings.view'))
                                                <li><a href="{{ url('saving/'.$key->id.'/show') }}"><i
                                                                class="fa fa-search"></i> {{ trans_choice('general.detail',2) }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('savings.update'))
                                                <li><a href="{{ url('saving/'.$key->id.'/edit') }}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('savings.delete'))
                                                <li><a href="{{ url('saving/'.$key->id.'/delete') }}"
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
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [4]}
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
