@extends('layouts.master')
@section('title'){{trans_choice('general.payroll',1)}} {{trans_choice('general.template',2)}}
@endsection
@section('content')
    <div class="row">
        @foreach($data as $key)
            <div class="col-md-4">
                <div class="img-thumbnail text-center">
                    <a href="{{url('payroll/template/'.$key->id.'/edit')}}">
                        <img src="{{asset('uploads/default_payroll_template.jpg')}}" class="img-responsive"/>
                        <h4>{{$key->name}}</h4>

                        <p>{{$key->notes}}</p>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
            $('.deletePayment').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: 'Are you sure?',
                    text: 'If you delete a payment, a fully paid loan may change status to open.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok',
                    cancelButtonText: 'Cancel'
                }).then(function () {
                    window.location = href;
                })
            });
        });
    </script>

@endsection
