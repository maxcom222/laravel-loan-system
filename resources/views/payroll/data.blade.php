@extends('layouts.master')
@section('title'){{trans_choice('general.payroll',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.payroll',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="view-repayments"
                       class="table table-striped table-condensed table-hover no-footer">
                    <thead>
                    <tr  role="row">
                        <th>
                            {{trans_choice('general.staff',1)}}
                        </th>
                        <th>{{trans_choice('general.last_pay_date',1)}}</th>
                        <th>
                            {{trans_choice('general.last_gross_amount',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.last_total_deductions',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.last_paid_amount',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.last_payslip',1)}}
                        </th>
                        <th>
                            {{trans_choice('general.action',1)}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{$key->first_name}} {{$key->last_name}}</td>
                            @if(!empty(\App\Models\Payroll::where('user_id',$key->id)->orderBy('created_at','desc')->first()))
                                <td>
                                    {{\App\Models\Payroll::where('user_id',$key->id)->orderBy('created_at','desc')->first()->date}}
                                </td>
                                <td>
                                    {{\App\Helpers\GeneralHelper::single_payroll_total_pay(\App\Models\Payroll::where('user_id',$key->id)->orderBy('created_at','desc')->first()->id)}}
                                </td>
                                <td>
                                    {{\App\Helpers\GeneralHelper::single_payroll_total_deductions(\App\Models\Payroll::where('user_id',$key->id)->orderBy('created_at','desc')->first()->id)}}
                                </td>
                                <td>{{\App\Models\Payroll::where('user_id',$key->id)->orderBy('created_at','desc')->first()->paid_amount}}</td>
                                <td>
                                    <div class="btn-group-horizontal">
                                        <a type="button" class="btn-xs bg-blue"
                                           href="{{url('payroll/'.\App\Models\Payroll::where('user_id',$key->id)->orderBy('created_at','desc')->first()->id.'/payslip')}}"
                                           target="_blank">{{trans_choice('general.generate_payslip',1)}}</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group-horizontal">
                                        @if(Sentinel::hasAccess('payroll.create'))
                                            <a type="button" class="btn-xs bg-navy"
                                               href="{{url('payroll/'.$key->id.'/data')}}">{{trans_choice('general.view_all_payroll',1)}}</a>
                                        @endif
                                    </div>
                                </td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="btn-group-horizontal">
                                        @if(Sentinel::hasAccess('payroll.create'))
                                            <a type="button" class="btn-xs bg-navy"
                                               href="{{url('payroll/'.$key->id.'/data')}}">{{trans_choice('general.view_all_payroll',1)}}</a>
                                        @endif
                                    </div>
                                </td>
                            @endif
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
                    title: 'Are you sure?',
                    text: 'If you delete a payment, a fully paid loan may change status to open.',
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
                {"orderable": false, "targets": [5, 6]}
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
