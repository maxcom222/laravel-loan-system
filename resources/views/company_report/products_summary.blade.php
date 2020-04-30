@extends('layouts.master')
@section('title')
    {{trans_choice('general.product',2)}} {{trans_choice('general.summary',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.product',2)}} {{trans_choice('general.summary',1)}}
                @if(!empty($end_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body hidden-print">
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label('start_date',trans_choice('general.start',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('start_date',$start_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('end_date',trans_choice('general.end',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('end_date',$end_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">

                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!
                        </button>


                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/company_report/products_summary/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/company_report/products_summary/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/company_report/products_summary/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    @if(!empty($end_date))
        <div class="panel panel-white">
            <div class="panel-body table-responsive no-padding">

                <table class="table table-bordered table-condensed table-hover ">
                    <thead>
                    <tr class="bg-green font-11">
                        <th></th>
                        <th colspan="5">{{trans_choice('general.total',1)}} {{trans_choice('general.disbursed',1)}}</th>
                        <th colspan="5">{{trans_choice('general.outstanding',1)}}</th>
                    </tr>
                    <tr class="bg-green font-11">
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.loan',2)}}</th>
                        <th>{{trans_choice('general.principal',1)}}</th>
                        <th>{{trans_choice('general.interest',1)}}</th>
                        <th>{{trans_choice('general.fee',2)}}</th>
                        <th>{{trans_choice('general.total',1)}}</th>
                        <th>{{trans_choice('general.principal',1)}}</th>
                        <th>{{trans_choice('general.interest',1)}}</th>
                        <th>{{trans_choice('general.fee',2)}}</th>
                        <th>{{trans_choice('general.penalty',1)}}</th>
                        <th>{{trans_choice('general.total',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_disbursed = 0;
                    $total_disbursed_loans = 0;
                    $total_disbursed_principal = 0;
                    $total_disbursed_interest = 0;
                    $total_disbursed_fees = 0;
                    $total_disbursed_penalty = 0;
                    $total_outstanding = 0;
                    $total_outstanding_principal = 0;
                    $total_outstanding_interest = 0;
                    $total_outstanding_fees = 0;
                    $total_outstanding_penalty = 0;

                    ?>
                    @foreach(\App\Models\LoanProduct::get() as $key)
                        <?php
                        $principal_disbursed = 0;
                        $interest_disbursed = 0;
                        $fees_disbursed = 0;
                        $penalty_disbursed = 0;
                        $principal_outstanding = 0;
                        $interest_outstanding = 0;
                        $fees_outstanding = 0;
                        $penalty_outstanding = 0;
                        $disbursed_loans = 0;
                        $disbursed = 0;
                        $outstanding = 0;
                        //loop through loans, this will need to be improved
                        foreach (\App\Models\Loan::where('loan_product_id', $key->id)->where('branch_id',
                            session('branch_id'))->whereIn('status',
                            ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                            [$start_date, $end_date])->get() as $loan) {
                            $disbursed_loans = $disbursed_loans + 1;
                            $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                            $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id);
                            $principal_disbursed =$principal_disbursed+ $loan_due_items["principal"];
                            $interest_disbursed =$interest_disbursed+ $loan_due_items["interest"];
                            $fees_disbursed =$fees_disbursed+ $loan_due_items["fees"];
                            $penalty_disbursed =$penalty_disbursed+ $loan_due_items["penalty"];
                            $principal_outstanding =$principal_outstanding+ $loan_due_items["principal"] - $loan_paid_items["principal"];
                            $interest_outstanding =$interest_outstanding+ $loan_due_items["interest"] - $loan_paid_items["interest"];
                            $fees_outstanding =$fees_outstanding+ $loan_due_items["fees"] - $loan_paid_items["fees"];
                            $penalty_outstanding =$penalty_outstanding+ $loan_due_items["penalty"] - $loan_paid_items["penalty"];
                        }
                        $disbursed = $principal_disbursed + $interest_disbursed + $fees_disbursed;
                        $outstanding = $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;
                        $total_disbursed = $total_disbursed + $disbursed;
                        $total_disbursed_loans = $total_disbursed_loans + $disbursed_loans;
                        $total_disbursed_principal = $total_disbursed_principal + $principal_disbursed;
                        $total_disbursed_interest = $total_disbursed_interest + $interest_disbursed;
                        $total_disbursed_fees = $total_disbursed_fees + $fees_disbursed;
                        $total_disbursed_penalty = $total_disbursed_penalty + $penalty_disbursed;
                        $total_outstanding_principal = $total_outstanding_principal + $principal_outstanding;
                        $total_outstanding_interest = $total_outstanding_interest + $interest_outstanding;
                        $total_outstanding_fees = $total_outstanding_fees + $fees_outstanding;
                        $total_outstanding_penalty = $total_outstanding_penalty + $penalty_outstanding;
                        $total_outstanding = $total_outstanding + $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;

                        ?>
                        <tr>
                            <td>{{$key->name}}</td>
                            <td>{{$disbursed_loans}}</td>
                            <td>{{number_format($principal_disbursed,2)}}</td>
                            <td>{{number_format($interest_disbursed,2)}}</td>
                            <td>{{number_format($fees_disbursed,2)}}</td>
                            <td>{{number_format($disbursed,2)}}</td>
                            <td>{{number_format($principal_outstanding,2)}}</td>
                            <td>{{number_format($interest_outstanding,2)}}</td>
                            <td>{{number_format($fees_outstanding,2)}}</td>
                            <td>{{number_format($penalty_outstanding,2)}}</td>
                            <td>{{number_format($outstanding,2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <td>{{$total_disbursed_loans}}</td>
                        <td>{{number_format($total_disbursed_principal,2)}}</td>
                        <td>{{number_format($total_disbursed_interest,2)}}</td>
                        <td>{{number_format($total_disbursed_fees,2)}}</td>
                        <td>{{number_format($total_disbursed,2)}}</td>
                        <td>{{number_format($total_outstanding_principal,2)}}</td>
                        <td>{{number_format($total_outstanding_interest,2)}}</td>
                        <td>{{number_format($total_outstanding_fees,2)}}</td>
                        <td>{{number_format($total_outstanding_penalty,2)}}</td>
                        <td>{{number_format($total_outstanding,2)}}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $("body").addClass('sidebar-xs');
            });
        </script>
    @endif
@endsection
@section('footer-scripts')

@endsection
