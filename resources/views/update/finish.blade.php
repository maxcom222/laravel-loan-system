@extends('layouts.master')
@section('title'){{trans_choice('general.update_successful',2)}}
@endsection
@section('content')
    <div class="box box-default">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.update_successful',2)}} </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
                <div class="callout callout-success">
                    <h4>{{trans_choice('general.update_successful',1)}}</h4>

                    <p>System was updated successfully. If you encounter any problems you can <a href="mailto:info@webstudio.co.zw?subject=Problems after update">Contact Us</a> </p>
                </div>

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
@endsection
