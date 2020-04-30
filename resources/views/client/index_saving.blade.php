@extends('client.layout')
@section('title')
    {{trans_choice('general.saving',2)}} {{trans_choice('general.account',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.saving',2)}} {{trans_choice('general.account',2)}}</h6>
            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body ">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.account',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.balance',1)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td><a href="{{ url('saving/'.$key->id.'/show') }}">{{ $key->id }}</a></td>

                            <td>
                                @if(!empty($key->savings_product))
                                    {{ $key->savings_product->name }}
                                @endif
                            </td>
                            <td>{{ number_format(\App\Helpers\GeneralHelper::savings_account_balance($key->id),2) }}</td>
                            <td>
                                <a href="{{ url('client/saving/'.$key->id.'/show') }}" class="btn btn-info btn-xs"  data-toggle="tooltip"
                                   data-title="{{ trans_choice('general.detail',2) }}"><i
                                            class="fa fa-search"></i>
                                </a>


                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [3]}
            ],
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
