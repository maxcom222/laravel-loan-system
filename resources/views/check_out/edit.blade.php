@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.check_in',1) }}
@endsection

@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.check_in',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('check_in/'.$product_check_in->id.'/update'), 'method' => 'post','class'=>'', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('date',trans_choice('general.date',1),array('class'=>'')) !!}
                        {!! Form::text('date',$product_check_in->date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('supplier_id',trans_choice('general.supplier',1),array('class'=>'')) !!}
                        {!! Form::select('supplier_id',$suppliers,$product_check_in->supplier_id, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'required')) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('warehouse_id',trans_choice('general.warehouse',1),array('class'=>'')) !!}
                        {!! Form::select('warehouse_id',$warehouses,$product_check_in->warehouse_id, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'required')) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('products',trans_choice('general.product',1),array('class'=>'')) !!}
                        {!! Form::select('products',$products,null, array('class' => 'form-control select2', 'placeholder'=>trans_choice('general.select',1).' '.trans_choice('general.product',1),'id'=>'products')) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 ">
                    <h4>{{trans_choice('general.order',1)}} {{trans_choice('general.item',2)}}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered " id="items_table">
                            <thead>
                            <tr>
                                <th><strong>{{trans_choice('general.item',1)}}</strong></th>
                                <th><strong>{{trans_choice('general.qty',1)}}</strong></th>
                                <th><strong>{{trans_choice('general.unit_cost',1)}}</strong></th>
                                <th><strong>{{trans_choice('general.total',1)}}</strong></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="items_area">

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>{{trans_choice('general.total',1)}}</strong>
                                </td>
                                <td>
                                    <div class="total_amount"></div>
                                </td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary pull-right" name="save_return"
                    value="save_return">{{ trans_choice('general.save',1) }}
            </button>
        </div>
    {!! Form::close() !!}
    <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function (e) {

            $('#products').change(function (e) {
                if ($('#products').val() != "") {
                    $.ajax({
                        type: 'GET',
                        url: "{!!  url('check_in') !!}/" + $('#products').val() + "/get_product_data",
                        success: function (data) {
                            $("#items_area").append(data);
                            doCalc();
                        }
                    });
                } else {

                }
            })
        });
        function doCalc() {
            var total = 0;
            $('tr').each(function () {
                $(this).find('span.amount').html($('input:eq(2)', this).val() * $('input:eq(3)', this).val());
            });
            $('.amount').each(function () {
                total += parseInt($(this).html(), 10);
            });
            $('div.total_amount').html(total);

        }
        function deleteRow(d) {
            var id = $(d).data("id");
            $("#" + id).remove();
            doCalc();
        }
    </script>
@endsection

