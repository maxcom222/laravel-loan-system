@extends('layouts.master')
@section('title'){{trans_choice('general.override',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.due',1)}} {{trans_choice('general.amount',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.override',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.due',1)}} {{trans_choice('general.amount',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/'.$loan->id.'/override'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="callout callout-info">
                <p>You can change the <b>Loan Due</b> amount below by overriding it with a different value. This is
                    useful if you have changed your repayment schedule and it is different than the system generated
                    Loan Due amount.</p>
            </div>
            <div class="form-group">
                <div class="checkbox col-sm-offset-3 col-sm-9">
                    <label>
                        <input type="checkbox" name="override" id="override" value="{{$loan->override}}"> <b>Override
                            Loan Due Amount?</b>
                    </label>
                </div>

            </div>
            <div class="form-group">
                <label for="inputLoanManualDueAmount" class="col-sm-3 control-label">Manual Loan Due Amount</label>

                <div class="col-sm-9">
                    <input type="text" name="balance" class="form-control touchspin" id="balance"
                           placeholder="Amount" value="{{$loan->balance}}">
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
        </div>
        {!! Form::close() !!}
                <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
            if ($('#override').val() == 1) {
                $('#balance').removeAttr('disabled')
                $('#override').attr('checked', 'checked')
            } else {
                $('#balance').attr('disabled', 'disabled')
                $('#balance').val('0')
                $('#override').removeAttr('checked')
            }
            $("#override").on('ifChecked', function (e) {
                $('#override').val('1')
                $('#balance').removeAttr('disabled')
            });
            $("#override").on('ifUnchecked', function (e) {
                $('#override').val('0')
                $('#balance').val('0')
                $('#balance').attr('disabled', 'disabled')
            });
        });
    </script>
@endsection
