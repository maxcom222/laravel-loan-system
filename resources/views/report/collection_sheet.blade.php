@extends('layouts.master')
@section('title')
    {{trans_choice('general.collection',1)}}  {{trans_choice('general.sheet',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title hidden-print">
                {{trans_choice('general.collection',1)}}  {{trans_choice('general.sheet',1)}}
                @if(!empty($start_date))
                    for  <b>{{$start_date}}</b>
                @endif
            </h6>
            <div class="heading-elements">
                <button class="btn btn-sm btn-info hidden-print" onclick="window.print()">Print</button>
            </div>
        </div>
        <div class="panel-body hidden-print">
            <h4 class="">{{trans_choice('general.date',1)}} {{trans_choice('general.range',1)}}</h4>
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-xs-4">
                    {!! Form::text('start_date',$start_date, array('class' => 'form-control date-picker', 'placeholder'=>"Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-4">
                    {!! Form::select('user_id',$users,null, array('class' => 'form-control select2', 'placeholder'=>"Select Loan Officer",'required'=>'required')) !!}
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-2">
                        <span class="input-group-btn">
                          <button type="submit" class="btn btn-info">{{trans_choice('general.search',1)}}!
                          </button>
                        </span>
                        <span class="input-group-btn">
                          <a href="{{Request::url()}}"
                             class="btn bg-purple  btn-flat pull-right">{{trans_choice('general.reset',1)}}!</a>
                        </span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    @if(Request::isMethod('post'))
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    {{trans_choice('general.collection',1)}}  {{trans_choice('general.sheet',1)}}
                    @if(!empty($start_date))
                        for {{$user->first_name}} {{$user->last_name}} on <b>{{$start_date}}</b>
                    @endif
                </h6>
                <div class="heading-elements">
                    <button class="btn btn-sm btn-info hidden-print" onclick="window.print()">Print</button>
                </div>
            </div>
            <div class="panel-body table-responsive">
                <table id="reports_table" class="table table-condensed  table-hover ">
                    <thead>
                    <tr>
                        <th>
                            <b>{{trans_choice('general.borrower',1)}}</b>
                        </th>
                        <th>
                            <b>{{trans_choice('general.phone',1)}}</b>
                        </th>
                        <th>
                            <b>{{trans_choice('general.loan',1)}}</b>
                        </th>
                        <th>{{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.due',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_amount = 0;
                    $total_due = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $total_amount = $total_amount + $key->principal + $key->interest + $key->fees + $key->penalty;
                        $total_due = $total_due + \App\Helpers\GeneralHelper::schedule_due_amount($key->id);
                        ?>
                        <tr>
                            <td class="">
                                {{$key->borrower->first_name}} {{$key->borrower->last_name}}
                                -{{$key->borrower->unique_number}}
                            </td>
                            <td>
                                {{$key->borrower->mobile}}
                            </td>
                            <td>
                                #{{$key->loan_id}}
                            </td>
                            <td>
                                {{ number_format($key->principal+$key->interest+$key->fees+$key->penalty)}}
                            </td>
                            <td>
                                {{number_format(\App\Helpers\GeneralHelper::schedule_due_amount($key->id))}}

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3"><b>{{trans_choice('general.total',1)}}</b></td>
                        <td><b>{{$total_amount}}</b></td>
                        <td><b>{{$total_due}}</b></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

@endsection
@section('footer-scripts')

    <script>
        $('#reports_table').DataTable({

            "paging": false,
            "lengthChange": true,
            "displayLength": 15,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": true,
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
