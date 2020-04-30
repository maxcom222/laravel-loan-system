@extends('layouts.master')
@section('title')
    {{ $borrower_group->name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ $borrower_group->name }}</h6>
                    <div class="heading-elements">
                    </div>
                </div>
                <div class="panel-body">
                    {!! $borrower_group->notes !!}
                </div>
                <!-- /.panel-body -->
                <div class="panel-footer">
                    <div class="heading-elements">
                        <span class="heading-text"> {{ trans_choice('general.created_at',1) }}
                            : {{$borrower_group->created_at}}</span>
                    </div>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-8">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ trans_choice('general.borrower',2) }}</h6>

                    <div class="heading-elements">
                        @if(Sentinel::hasAccess('borrowers.groups'))
                            <a href="#" data-toggle="modal" data-target="#addBorrower"
                               class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.borrower',1)}}</a>
                        @endif
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="" class="table table-striped table-condensed table-hover basic-data-table">
                            <thead>
                            <tr>
                                <th>{{trans_choice('general.id',1)}}</th>
                                <th>{{trans_choice('general.name',1)}}</th>
                                <th>{{trans_choice('general.mobile',1)}}</th>
                                <th>{{trans_choice('general.balance',1)}}</th>
                                <th>{{ trans_choice('general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($borrower_group->members as $key)
                                @if(!empty($key->borrower))
                                    <tr>
                                        <td>{{ $key->borrower->id }}</td>
                                        <td>{{ $key->borrower->first_name }} {{ $key->borrower->last_name }}</td>
                                        <td>{{ $key->borrower->mobile }}</td>
                                        <td>
                                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{ round(\App\Helpers\GeneralHelper::borrower_loans_total_due($key->borrower_id),2) }}
                                            @else
                                                {{ round(\App\Helpers\GeneralHelper::borrower_loans_total_due($key->borrower_id),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <ul class="icons-list">
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                            <i class="icon-menu9"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                            @if(Sentinel::hasAccess('borrowers.view'))
                                                                <li>
                                                                    <a href="{{ url('borrower/'.$key->borrower->id.'/show') }}"><i
                                                                                class="fa fa-search"></i> {{trans_choice('general.detail',2)}}
                                                                    </a></li>
                                                            @endif
                                                            @if(Sentinel::hasAccess('borrowers.update'))
                                                                <li>
                                                                    <a href="{{ url('borrower/'.$key->borrower->id.'/edit') }}"><i
                                                                                class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if(Sentinel::hasAccess('borrowers.groups'))
                                                                <li>
                                                                    <a href="{{ url('borrower/group/'.$key->id.'/remove_borrower') }}"
                                                                       class="delete"><i
                                                                                class="fa fa-trash"></i> {{ trans('general.remove') }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </li>
                                                </ul>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
            <!-- /.box -->
        </div>
    </div>
    <div class="modal fade" id="addBorrower">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">*</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.borrower',1)}}</h4>
                </div>
                {!! Form::open(array('url' => url('borrower/group/'.$borrower_group->id.'/add_borrower'),'method'=>'post')) !!}
                <div class="modal-body">
                    <div class="form-group">
                        <div class="form-line">
                            {!!  Form::label('borrower_id',trans_choice('general.borrower',1),array('class'=>' control-label')) !!}
                            {!! Form::select('borrower_id',$borrowers,null,array('class'=>' select2','placeholder'=>trans_choice('general.select',1),'required'=>'required')) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">{{trans_choice('general.save',1)}}</button>
                    <button type="button" class="btn default"
                            data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection