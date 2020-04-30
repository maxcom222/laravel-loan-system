@extends('layouts.master')
@section('title')
    {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',2)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body ">
            <div class="table-responsive">
                <table id="repayments-data-table" class="table  table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>
                            {{trans_choice('general.id',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.date',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.type',1)}}
                        </th>

                        <th>
                            {{trans_choice('general.debit',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.credit',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.detail',2)}}
                        </th>
                        <th class="text-center">
                            {{trans_choice('general.action',1)}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>{{$key->date}} {{$key->time}}</td>
                            <td>{{$key->created_at}}</td>
                            <td>
                                @if($key->type=='deposit')
                                    {{trans_choice('general.deposit',1)}}
                                @endif
                                @if($key->type=='withdrawal')
                                    {{trans_choice('general.withdrawal',1)}}
                                @endif
                                @if($key->type=='bank_fees')
                                    {{trans_choice('general.charge',1)}}
                                @endif
                                @if($key->type=='interest')
                                    {{trans_choice('general.interest',1)}}
                                @endif
                                @if($key->type=='dividend')
                                    {{trans_choice('general.dividend',1)}}
                                @endif
                                @if($key->type=='transfer')
                                    {{trans_choice('general.transfer',1)}}
                                @endif
                                @if($key->type=='transfer_fund')
                                    {{trans_choice('general.transfer',1)}}
                                @endif
                                @if($key->type=='transfer_loan')
                                    {{trans_choice('general.transfer',1)}}
                                @endif
                                @if($key->type=='guarantee')
                                    {{trans_choice('general.on',1)}} {{trans_choice('general.hold',1)}}
                                @endif
                                @if($key->reversed==1)
                                    @if($key->reversal_type=="user")
                                        <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                                )</b></span>
                                    @endif
                                    @if($key->reversal_type=="system")
                                        <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                                )</b></span>
                                    @endif
                                @endif
                            </td>
                            <td>{{number_format($key->debit,2)}}</td>
                            <td>{{number_format($key->credit,2)}}</td>
                            <td>{{$key->receipt}}</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle"
                                           data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @if($key->reversed==0)
                                                <li>
                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/show')}}"><i
                                                                class="fa fa-search"></i> {{ trans_choice('general.view',1) }}
                                                    </a></li>
                                                <li>
                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/print')}}"
                                                       target="_blank"><i
                                                                class="icon-printer"></i> {{ trans_choice('general.print',1) }} {{trans_choice('general.receipt',1)}}
                                                    </a></li>
                                                <li>
                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/pdf')}}"
                                                       target="_blank"><i
                                                                class="icon-file-pdf"></i> {{ trans_choice('general.pdf',1) }} {{trans_choice('general.receipt',1)}}
                                                    </a></li>
                                            @endif
                                            @if($key->reversed==0 && $key->reversible==1)

                                                <li>
                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/edit')}}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                    </a></li>
                                                <li>
                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/reverse')}}"
                                                       class="delete"><i
                                                                class="fa fa-minus-circle"></i> {{ trans('general.reverse') }}
                                                    </a></li>
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
        $('#repayments-data-table').DataTable({
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [7]
            }],
            "order": [[1, "asc"]],
            language: {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}:",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            drawCallback: function () {
                $('.delete').on('click', function (e) {
                    e.preventDefault();
                    var href = $(this).attr('href');
                    swal({
                        title: 'Are you sure?',
                        text: '',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok',
                        cancelButtonText: 'Cancel'
                    }).then(function () {
                        window.location = href;
                    })
                });
            }
        });
    </script>
@endsection
