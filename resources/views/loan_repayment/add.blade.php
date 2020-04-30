@extends('layouts.master')
@section('title'){{trans_choice('general.add',1)}} {{trans_choice('general.repayment',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.repayment',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form class="form-horizontal">
            <div class="panel-body">
                <div class="form-group">
                    {!! Form::label('loan_id',trans_choice('general.loan',1),array('class'=>'col-sm-3 control-label')) !!}
                    <div class="col-sm-5">
                        {!! Form::select('loan_id',$loans,null, array('class' => ' form-control select2','placeholder'=>trans_choice('general.select',1),'required'=>'required','id'=>'loan_id')) !!}
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
            <div class="panel-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right" id="add_repayment">{{trans_choice('general.next',1)}}</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.box -->
    <script>
        $("#add_repayment").click(function (e) {
            e.preventDefault();
            if($("#loan_id").val()===""){
                alert("Please select loan")
            }else{
                window.location="{{url('loan/')}}/"+$("#loan_id").val()+"/repayment/create";
            }
        })
    </script>
@endsection

