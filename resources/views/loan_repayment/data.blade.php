@extends('layouts.master')
@section('title'){{trans_choice('general.repayment',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.repayment',2)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="repayments-data-table"
                       class="table  table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>
                            {{trans_choice('general.loan',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.collection',1)}} {{trans_choice('general.date',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.collected_by',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.method',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.receipt',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.amount',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.action',1)}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td><a href="{{url('loan/'.$key->loan_id.'/show')}}"> {{$key->loan_id}}</a></td>
                            <td>{{$key->date}}</td>
                            <td>
                                @if(!empty($key->user))
                                    {{$key->user->first_name}} {{$key->user->last_name}}
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->loan_repayment_method))
                                    {{$key->loan_repayment_method->name}}
                                @endif
                            </td>
                            <td>{{$key->receipt}}</td>
                            <td>
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($key->credit,2)}}
                                @else
                                    {{number_format($key->credit,2)}}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                                @endif
                            </td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="{{url('loan/transaction/'.$key->id.'/show')}}"><i
                                                            class="fa fa-search"></i> {{ trans_choice('general.view',1) }}
                                                </a></li>
                                            <li>

                                            @if($key->transaction_type=='repayment' && $key->reversible==1)
                                                <li>
                                                    <a href="{{url('loan/transaction/'.$key->id.'/print')}}"
                                                       target="_blank"><i
                                                                class="icon-printer"></i> {{ trans_choice('general.print',1) }} {{trans_choice('general.receipt',1)}}
                                                    </a></li>
                                                <li>
                                                    <a href="{{url('loan/transaction/'.$key->id.'/pdf')}}"
                                                       target="_blank"><i
                                                                class="icon-file-pdf"></i> {{ trans_choice('general.pdf',1) }} {{trans_choice('general.receipt',1)}}
                                                    </a></li>
                                                <li>
                                                    <a href="{{url('loan/repayment/'.$key->id.'/edit')}}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                    </a></li>
                                                <li>
                                                    <a href="{{url('loan/repayment/'.$key->id.'/reverse')}}"
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
        $(document).ready(function () {
            $('.deletePayment').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: '{{trans_choice('general.are_you_sure',1)}}',
                    text: '{{trans_choice('general.delete_payment_msg',1)}}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{trans_choice('general.ok',1)}}',
                    cancelButtonText: '{{trans_choice('general.cancel',1)}}'
                }).then(function () {
                    window.location = href;
                })
            });
        });
    </script>

    <script>

        $('#repayments-data-table').DataTable({
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [6]
            }],
            "order": [[1, "desc"]],
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
        });
    </script>
@endsection
