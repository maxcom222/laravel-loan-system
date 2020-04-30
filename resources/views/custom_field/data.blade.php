@extends('layouts.master')
@section('title')
    {{trans_choice('general.custom_field',2)}}
@endsection

@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.custom_field',2)}}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('custom_fields.create'))
                    <a href="{{ url('custom_field/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.custom_field',2)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table id="data-table" class="table table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>{{trans_choice('general.name',1)}}</th>
                    <th>{{trans_choice('general.category',1)}}</th>
                    <th>{{trans_choice('general.required',1)}} {{trans_choice('general.field',1)}}</th>
                    <th>{{trans_choice('general.type',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>
                            @if($key->category=="borrowers")
                                {{trans_choice('general.add',1)}} {{trans_choice('general.borrower',1)}}
                            @endif
                            @if($key->category=="expenses")
                                {{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}
                            @endif
                            @if($key->category=="other_income")
                                {{trans_choice('general.add',1)}} {{trans_choice('general.other_income',1)}}
                            @endif
                            @if($key->category=="collateral")
                                {{trans_choice('general.add',1)}} {{trans_choice('general.collateral',1)}}
                            @endif
                            @if($key->category=="loans")
                                {{trans_choice('general.add',1)}} {{trans_choice('general.loan',1)}}
                            @endif
                            @if($key->category=="repayments")
                                {{trans_choice('general.add',1)}} {{trans_choice('general.repayment',1)}}
                            @endif
                        </td>
                        <td>
                            @if($key->required==0)
                                {{trans_choice('general.no',1)}}
                            @else
                                {{trans_choice('general.yes',1)}}
                            @endif
                        </td>
                        <td>
                            @if($key->field_type=="number")
                                {{trans_choice('general.number_field',1)}}
                            @endif
                            @if($key->field_type=="textfield")
                                {{trans_choice('general.text_field',1)}}
                            @endif
                            @if($key->field_type=="textarea")
                                {{trans_choice('general.textarea',1)}}
                            @endif
                            @if($key->field_type=="decimal")
                                {{trans_choice('general.decimal_field',1)}}
                            @endif
                            @if($key->field_type=="date")
                                {{trans_choice('general.date_field',1)}}
                            @endif
                        </td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @if(Sentinel::hasAccess('custom_fields.update'))
                                            <li><a href="{{ url('custom_field/'.$key->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('custom_fields.delete'))
                                            <li><a href="{{ url('custom_field/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i> {{ trans('general.delete') }} </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

@endsection
