@extends('layouts.master')
@section('title'){{$user->first_name}} {{$user->last_name}}-{{trans_choice('general.payroll',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{$user->first_name}} {{$user->last_name}}-{{trans_choice('general.payroll',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="view-repayments"
                       class="table table-striped table-condensed table-hover dataTable no-footer">
                    <thead>
                    <tr role="row">
                        <th>{{trans_choice('general.pay',1)}} {{trans_choice('general.date',1)}}</th>
                        <th>
                            {{trans_choice('general.gross',1)}} {{trans_choice('general.amount',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.total',1)}} {{trans_choice('general.deduction',2)}}
                        </th>
                        <th>
                            {{trans_choice('general.net',1)}} {{trans_choice('general.amount',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.paid',1)}} {{trans_choice('general.amount',1)}}
                        </th>
                        <th>{{trans_choice('general.recurring',1)}}</th>
                        <th>
                            {{trans_choice('general.payslip',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.action',1)}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user->payroll as $key)
                        <tr>
                            <td>
                                {{$key->date}}
                            </td>
                            <td>
                                {{\App\Helpers\GeneralHelper::single_payroll_total_pay($key->id)}}
                            </td>
                            <td>
                                {{\App\Helpers\GeneralHelper::single_payroll_total_deductions($key->id)}}
                            </td>
                            <td>
                                {{\App\Helpers\GeneralHelper::single_payroll_total_pay($key->id)-\App\Helpers\GeneralHelper::single_payroll_total_deductions($key->id)}}
                            </td>
                            <td>{{$key->paid_amount}}</td>
                            <td>
                                @if($key->recurring==1)
                                    {{trans_choice('general.yes',1)}}
                                @else
                                    {{trans_choice('general.no',1)}}
                                @endif
                            </td>
                            <td>
                                <div class="btn-group-horizontal">
                                    <a type="button" class="btn-xs bg-blue"
                                       href="{{url('payroll/'.$key->id.'/payslip')}}"
                                       target="_blank">{{trans_choice('general.generate_payslip',1)}}</a>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group-horizontal">
                                    @if(Sentinel::hasAccess('payroll.delete'))
                                        <a type="button" class="btn-xs bg-purple"
                                           href="{{url('payroll/'.$key->id.'/edit')}}">{{trans_choice('general.view_modify',1)}}</a><a
                                                type="button" class="btn-xs bg-navy margin delete"
                                                href="{{url('payroll/'.$key->id.'/delete')}}">{{trans_choice('general.delete',1)}}</a>
                                    @endif
                                </div>
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
            $('.deletePayroll').on('click', function (e) {
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
        });
    </script>
    <script>

        $('#view-repayments').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [6, 7]}
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
