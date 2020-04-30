@extends('layouts.master')
@section('title')
    {{trans_choice('general.borrower',1)}} {{trans_choice('general.number',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.borrower',1)}} {{trans_choice('general.number',2)}}
                @if(!empty($end_date))
                    as at: <b> {{$end_date}}</b>
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
                                    <a href="{{url('report/borrower_report/borrower_numbers/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/borrower_report/borrower_numbers/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/borrower_report/borrower_numbers/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
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
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.value',1)}}</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_borrowers = 0;
                    $blacklisted_borrowers = 0;
                    $dormant_borrowers = 0;
                    $active_borrowers = 0;
                    $new_borrowers = 0;
                    foreach (\App\Models\Borrower::all() as $key) {
                        $total_borrowers = $total_borrowers + 1;
                        if ($key->blacklisted == 1) {
                            $blacklisted_borrowers = $blacklisted_borrowers + 1;
                        }
                        if ($start_date <=date_format(date_create($key->created_at),"Y-m-d ")  && $end_date >=date_format(date_create($key->created_at),"Y-m-d ") ) {
                            $new_borrowers = $new_borrowers + 1;
                        }
                        if (count($key->loans) > 0) {
                            $active_borrowers = $active_borrowers + 1;
                        } else {
                            $dormant_borrowers = $dormant_borrowers + 1;
                        }
                    }

                    ?>


                    <tr>
                        <td>
                            {{trans_choice('general.dormant',1)}} {{trans_choice('general.borrower',2)}}
                        </td>
                        <td>
                            {{$dormant_borrowers}}
                        </td>

                    </tr>
                    <tr>
                        <td>
                            {{trans_choice('general.new',1)}} {{trans_choice('general.borrower',2)}}
                        </td>
                        <td>
                            {{$new_borrowers}}
                        </td>

                    </tr>
                    <tr>
                        <td>
                            {{trans_choice('general.blacklisted',1)}} {{trans_choice('general.borrower',2)}}
                        </td>
                        <td>
                            {{$blacklisted_borrowers}}
                        </td>

                    </tr>
                    <tr>
                        <td>
                            {{trans_choice('general.total',1)}} {{trans_choice('general.borrower',2)}}
                        </td>
                        <td>
                            {{$total_borrowers}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                //$("body").addClass('sidebar-xs');
            });
        </script>
    @endif
@endsection
@section('footer-scripts')

@endsection
