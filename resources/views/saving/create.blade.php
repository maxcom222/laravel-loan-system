@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.account',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.account',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('saving/store'), 'method' => 'post', 'id' => 'savings_form',"enctype"=>"multipart/form-data", 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('borrower_id',trans_choice('general.borrower',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('borrower_id',$borrowers,$borrower_id, array('class' => ' select2 form-control', 'placeholder'=>"Select",'required'=>'required','id'=>'borrower_id')) !!}
                </div>
                <div class="col-sm-4">
                    <i class="icon-info3" data-toggle="tooltip" title="Select the borrower here"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('savings_product_id',trans_choice('general.product',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('savings_product_id',$savings_products,null, array('class' => 'select2 form-control','required'=>'','id'=>'savings_product_id')) !!}
                </div>
                <div class="col-sm-4">
                    <i class="icon-info3" data-toggle="tooltip" title="Select the Savings product"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.date',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('date',date("Y-m-d"), array('class' => 'form-control date-picker', 'placeholder'=>"yyyy-mm-dd",'required'=>'required','id'=>'date')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.note',2),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>'2',)) !!}
                </div>
            </div>
            <p class="bg-navy color-palette">{{trans_choice('general.charge',2)}}</p>

            <div class="form-group" id="chargesDiv">
                <div style="display: none;" id="saved_charges">
                    @foreach($savings_product->charges as $key)
                        <input name="charges[]" id="charge{{$key->charge_id}}" value="{{$key->charge_id}}">
                    @endforeach
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.type',1)}}</th>
                        <th>{{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.collected',1)}} {{trans_choice('general.on',1)}}</th>
                        <th>{{trans_choice('general.date',1)}}</th>
                    </tr>
                    </thead>
                    <tbody id="charges_table">
                    @foreach($savings_product->charges as $key)
                        @if(!empty($key->charge))
                            <tr id="row{{$key->charge->id}}">
                                <td>{{ $key->charge->name }}</td>
                                <td>
                                    @if($key->charge->charge_option=="fixed")
                                        {{trans_choice('general.fixed',1)}}
                                    @endif
                                    @if($key->charge->charge_option=="principal_due")
                                        % {{trans_choice('general.principal',1)}} {{trans_choice('general.due',1)}}
                                    @endif
                                    @if($key->charge->charge_option=="principal_interest")
                                        % {{trans_choice('general.principal',1)}}
                                        + {{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}
                                    @endif
                                    @if($key->charge->charge_option=="interest_due")
                                        % {{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}
                                    @endif
                                    @if($key->charge->charge_option=="total_due")
                                        % {{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}
                                    @endif
                                    @if($key->charge->charge_option=="original_principal")
                                        % {{trans_choice('general.original',1)}} {{trans_choice('general.principal',1)}}
                                    @endif
                                </td>
                                <td>
                                    @if($key->charge->override==1)
                                        <input type="text" class="form-control"
                                               name="charge_amount_{{$key->charge->id}}"
                                               value="{{$key->charge->amount}}" required>
                                    @else
                                        <input  type="hidden" class="form-control"
                                                name="charge_amount_{{$key->charge->id}}"
                                                value="{{$key->charge->amount}}">
                                        {{$key->charge->amount}}
                                    @endif
                                </td>
                                <td>
                                    @if($key->charge->charge_type=='disbursement')
                                        {{trans_choice('general.disbursement',1)}}
                                    @endif
                                    @if($key->charge->charge_type=='specified_due_date')
                                        {{trans_choice('general.specified_due_date',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='installment_fee')
                                        {{trans_choice('general.installment_fee',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='overdue_installment_fee')
                                        {{trans_choice('general.overdue_installment_fee',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='loan_rescheduling_fee')
                                        {{trans_choice('general.loan_rescheduling_fee',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='overdue_maturity')
                                        {{trans_choice('general.overdue_maturity',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='savings_activation')
                                        {{trans_choice('general.savings_activation',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='withdrawal_fee')
                                        {{trans_choice('general.withdrawal_fee',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='monthly_fee')
                                        {{trans_choice('general.monthly_fee',2)}}
                                    @endif
                                    @if($key->charge->charge_type=='annual_fee')
                                        {{trans_choice('general.annual_fee',2)}}
                                    @endif
                                </td>
                                <td>
                                    @if($key->charge->charge_type=='specified_due_date')
                                        <input type="text" class="form-control date-picker"
                                               name="charge_date_{{$key->charge->id}}"
                                               value="" required>
                                    @else
                                        <input  type="hidden" class="form-control"
                                                name="charge_date_{{$key->charge->id}}"
                                                value="">
                                    @endif
                                </td>

                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
@endsection

@section('footer-scripts')
    <script>
        $(document).ready(function () {

            $('#savings_product_id').change(function (e) {
                window.location = "{!! url('saving/create?product_id=') !!}" + $("#savings_product_id").val()+"&borrower_id="+ $("#borrower_id").val();
            })

        });
    </script>
    <script>
        $("#savings_form").validate({
            rules: {
                field: {
                    required: true,
                    number: true
                }
            }
        });
    </script>
@endsection