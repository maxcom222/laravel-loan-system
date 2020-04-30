@extends('layouts.master')
@section('title')
    {{trans_choice('general.collateral',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.collateral',1)}} </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <div class="">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr >
                        <th>{{trans_choice('general.type',1)}}</th>
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.borrower',1)}}</th>
                        <th>{{trans_choice('general.loan',1)}}</th>
                        <th>{{trans_choice('general.value',1)}}</th>
                        <th>{{trans_choice('general.status',1)}}</th>
                        <th>{{trans_choice('general.date',1)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                @if(!empty($key->collateral_type))
                                    {{$key->collateral_type->name}}
                                @endif
                            </td>
                            <td>{{ $key->name }}</td>
                            <td>
                                @if(!empty($key->borrower))
                                    <a href="{{url('borrower/'.$key->borrower_id.'/show')}}"> {{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                                @endif
                            </td>
                            <td><a href="{{url('loan/'.$key->loan_id.'/show')}}"># {{ $key->loan_id }}</a></td>
                            <td>{{ $key->value }}</td>
                            <td>
                                @if($key->status=='deposited_into_branch')
                                    {{trans_choice('general.deposited_into_branch',1)}}
                                @endif
                                @if($key->status=='collateral_with_borrower')
                                    {{trans_choice('general.collateral_with_borrower',1)}}
                                @endif
                                @if($key->status=='returned_to_borrower')
                                    {{trans_choice('general.returned_to_borrower',1)}}
                                @endif
                                @if($key->status=='repossession_initiated')
                                    {{trans_choice('general.repossession_initiated',1)}}
                                @endif
                                @if($key->status=='repossessed')
                                    {{trans_choice('general.repossessed',1)}}
                                @endif
                                @if($key->status=='sold')
                                    {{trans_choice('general.sold',1)}}
                                @endif
                                @if($key->status=='lost')
                                    {{trans_choice('general.lost',1)}}
                                @endif
                            </td>
                            <td>{{ $key->date }}</td>
                            <td>
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @if(Sentinel::hasAccess('collateral.view'))
                                                <li><a href="{{ url('collateral/'.$key->id.'/show') }}"><i
                                                                class="fa fa-search"></i> {{ trans('general.view') }}
                                                    </a></li>
                                            @endif
                                            @if(Sentinel::hasAccess('collateral.update'))
                                                <li><a href="{{ url('collateral/'.$key->id.'/edit') }}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('collateral.delete'))
                                                <li><a href="{{ url('collateral/'.$key->id.'/delete') }}"
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

            "order": [[6, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [7]}
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
            }
        });
    </script>
@endsection
