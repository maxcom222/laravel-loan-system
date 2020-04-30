@extends('layouts.master')
@section('title'){{trans_choice('general.loan',1)}} {{trans_choice('general.application',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.loan',1)}} {{trans_choice('general.application',2)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body table-responsive">
            <table id="data-table" class="table table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{trans_choice('general.borrower',1)}}</th>
                    <th>{{trans_choice('general.product',1)}}</th>
                    <th>{{trans_choice('general.amount',1)}}</th>
                    <th>{{trans_choice('general.status',1)}}</th>
                    <th>{{trans_choice('general.note',2)}}</th>
                    <th>{{trans_choice('general.date',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{$key->id}}</td>
                        <td>
                            @if(!empty($key->borrower))
                                <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                            @else
                                <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                            class="fa fa-exclamation-triangle"></i> </span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->loan_product))
                                <a href="{{url('loan/loan_product/'.$key->loan_product_id.'/edit')}}">{{$key->loan_product->name}}</a>
                            @else
                                <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                            class="fa fa-exclamation-triangle"></i> </span>
                            @endif
                        </td>
                        <td>{{round($key->amount,2)}}</td>
                        <td>
                            @if($key->status=='declined')
                                <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                            @endif
                            @if($key->status=='approved')
                                <span class="label label-success">{{trans_choice('general.approved',1)}}</span>
                            @endif
                            @if($key->status=='pending')
                                <span class="label label-warning">{{trans_choice('general.pending',1)}}</span>
                            @endif
                        </td>

                        <td>{!! $key->notes !!}</td>
                        <td>{!! $key->created_at !!}</td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if($key->status=='pending' || $key->status=="declined")
                                            @if(Sentinel::hasAccess('loans.create'))
                                                <li><a href="{{ url('loan/loan_application/'.$key->id.'/approve') }}"><i
                                                                class="fa fa-check"></i> {{ trans('general.approve') }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endif
                                        @if( $key->status=="pending")
                                            @if(Sentinel::hasAccess('loans.update'))
                                                <li><a href="{{ url('loan/loan_application/'.$key->id.'/decline') }}"><i
                                                                class="fa fa-minus-circle"></i> {{ trans('general.decline') }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(Sentinel::hasAccess('loans.delete'))
                                            <li><a href="{{ url('loan/loan_application/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i> {{ trans('general.delete') }} </a>
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
