@extends('layouts.master')
@section('title'){{trans_choice('general.download',2)}} {{trans_choice('general.update',2)}}
@endsection
@section('content')
    <div class="box box-default">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.download',2)}} {{trans_choice('general.update',2)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            @if(file_exists(storage_path() . "/updates/update.zip"))
                <div class="callout callout-warning">
                    <h4>{{trans_choice('general.ready_to_update',1)}}</h4>

                    <p>{{trans_choice('general.backup_first',1)}}</p>
                </div>
                <a href="{{url('update/install')}}" class="btn btn-info delete">{{trans_choice('general.install',1)}} {{trans_choice('general.update',2)}}</a>
            @else
                <div class="callout callout-danger">

                    <p>Reload this page to try the download again</p>
                </div>
            @endif
        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
@endsection
