@extends('client.layout')
@section('title')
    {{ trans_choice('general.pay',1) }}
@endsection

@section('content')
    <style>
        /* CSS for Credit Card Payment form */
        .credit-card-box .panel-title {
            display: inline;
            font-weight: bold;
        }

        .credit-card-box .form-control.error {
            border-color: red;
            outline: 0;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(255, 0, 0, 0.6);
        }

        .credit-card-box label.error {
            font-weight: bold;
            color: red;
            padding: 2px 8px;
            margin-top: 2px;
        }

        .credit-card-box .payment-errors {
            font-weight: bold;
            color: red;
            padding: 2px 8px;
            margin-top: 2px;
        }

        .credit-card-box label {
            display: block;
        }

        /* The old "center div vertically" hack */
        .credit-card-box .display-table {
            display: table;
        }

        .credit-card-box .display-tr {
            display: table-row;
        }

        .credit-card-box .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        /* Just looks nicer */
        .credit-card-box .panel-heading img {
            min-width: 180px;
        }
    </style>
    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ trans_choice('general.pay',1) }}</h6>

                    <div class="heading-elements">
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('payment_gateway',trans_choice('general.payment',1).' '.trans_choice('general.method',1),array('class'=>'')) !!}
                        {!! Form::select('payment_gateway',$methods,null, array('class' => 'form-control','id'=>'method','required'=>'required')) !!}
                    </div>
                    <div>
                        {!! Form::open(array('url' => '', 'method' => 'post', 'id' => 'payment_form',"enctype"=>"multipart/form-data")) !!}

                        <div class="form-group">
                            {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'')) !!}
                            {!! Form::text('amount',null, array('class' => 'form-control touchspin', 'placeholder'=>'','required'=>'required')) !!}
                        </div>
                        <div class="form-group" id="mpesa_mobile">
                            {!! Form::label('mobile',trans_choice('general.mobile',1),array('class'=>'')) !!}
                            {!! Form::number('mobile',$borrower->mobile, array('class' => 'form-control', 'placeholder'=>'','id'=>'mobile')) !!}
                        </div>
                        <div class="col-md-6" style="display: none;">
                            <input name="rm" value="2" type="hidden">
                            <input name="cmd" value="_xclick" type="hidden">
                            <input name="currency_code"
                                   value="{{ \App\Models\Setting::where('setting_key', 'currency')->first()->setting_value }}"
                                   type="hidden">
                            <input name="quantity" value="1" type="hidden">
                            <input name="business"
                                   value="{{ \App\Models\Setting::where('setting_key', 'paypal_email')->first()->setting_value }}"
                                   type="hidden">
                            <input name="return" value="{{ url('client/saving/'.$saving->id.'/pay/paypal/done') }}"
                                   type="hidden">
                            <input name="cancel_return"
                                   value="{{ url('client/saving/'.$saving->id.'/pay/paypal/cancel') }}"
                                   type="hidden">
                            <input name="notify_url"
                                   value="{{ url('client/saving/pay/paypal/ipn') }}" type="hidden">
                            <input name="custom" value="" type="hidden">
                            <input name="item_name" value="Savings Deposit" type="hidden">
                            <input name="item_number" value="{{ $saving->id }}" type="hidden">
                        </div>

                        <button type="submit" class="btn btn-primary" id="pay">{{trans('general.pay')}}</button>
                        <p><br></p>
                        {!! Form::close() !!}
                    </div>
                    <div class="row" id="stripeForm">
                        <div class="col-md-6">
                            <div class="panel panel-default credit-card-box">
                                <div class="panel-heading display-table">
                                    <div class="row display-tr">
                                        <h3 class="panel-title display-td">Payment Details</h3>
                                        <div class="display-td">
                                            <img class="img-responsive pull-right"
                                                 src="http://i76.imgup.net/accepted_c22e0.png">
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <form role="form" id="payment-form" method="POST" action="javascript:void(0);">
                                        <div class="form-group">
                                            <div class="form-line">
                                                {!!  Form::label(trans_choice('general.amount',1),null,array('class'=>' control-label')) !!}
                                                {!! Form::text('amount',null,array('class'=>'form-control touchspin','required'=>'required','id'=>'stripe_amount')) !!}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label for="cardNumber">CARD NUMBER</label>
                                                    <div class="input-group">
                                                        <input
                                                                type="tel"
                                                                class="form-control"
                                                                name="cardNumber"
                                                                placeholder="Valid Card Number"
                                                                autocomplete="cc-number"
                                                                required autofocus
                                                        />
                                                        <span class="input-group-addon"><i
                                                                    class="fa fa-credit-card"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-7 col-md-7">
                                                <div class="form-group">
                                                    <label for="cardExpiry"><span
                                                                class="hidden-xs">EXPIRATION</span><span
                                                                class="visible-xs-inline">EXP</span> DATE</label>
                                                    <input
                                                            type="tel"
                                                            class="form-control"
                                                            name="cardExpiry"
                                                            placeholder="MM / YY"
                                                            autocomplete="cc-exp"
                                                            required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-xs-5 col-md-5 pull-right">
                                                <div class="form-group">
                                                    <label for="cardCVC">CV CODE</label>
                                                    <input
                                                            type="tel"
                                                            class="form-control"
                                                            name="cardCVC"
                                                            placeholder="CVC"
                                                            autocomplete="cc-csc"
                                                            required
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <button class="subscribe btn btn-success btn-lg btn-block"
                                                        type="button">{{trans('general.pay')}}
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row" style="display:none;">
                                            <div class="col-xs-12">
                                                <p class="payment-errors"></p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">

                </div>

            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
        });
    </script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script>
        if ($('#method').val() == 'paynow') {
            $('#stripeForm').hide();
            $('#payment_form').show();
            $('#mpesa_mobile').hide();
            $('#mobile').removeAttr("required");
            $('#payment_form').attr('action', "{!! url('client/saving/'.$saving->id.'/pay/paynow') !!}");
        }
        if ($('#method').val() == 'paypal') {
            $('#payment_form').attr('action', "https://www.paypal.com/cgi-bin/webscr");
            $('#stripeForm').hide();
            $('#mpesa_mobile').hide();
            $('#mobile').removeAttr("required");
            $('#payment_form').show();
        }
        if ($('#method').val() == 'mpesa_kenya') {
            $('#stripeForm').hide();
            $('#payment_form').show();
            $('#mpesa_mobile').show();
            $('#mobile').attr("required", "required");
            $('#payment_form').attr('action', "{!! url('client/saving/'.$saving->id.'/pay/mpesa') !!}");
        }
        if ($('#method').val() == 'stripe') {
            $('#payment_form').hide();
            $('#stripeForm').show();
        }
        $('#method').change(function (e) {
            if ($('#method').val() == 'paynow') {
                $('#stripeForm').hide();
                $('#payment_form').show();
                $('#mpesa_mobile').hide();
                $('#mobile').removeAttr("required");
                $('#payment_form').attr('action', "{!! url('client/saving/'.$saving->id.'/pay/paynow') !!}");
            }
            if ($('#method').val() == 'paypal') {
                $('#payment_form').attr('action', "https://www.paypal.com/cgi-bin/webscr");
                $('#stripeForm').hide();
                $('#mpesa_mobile').hide();
                $('#mobile').removeAttr("required");
                $('#payment_form').show();
            }
            if ($('#method').val() == 'mpesa_kenya') {
                $('#stripeForm').hide();
                $('#payment_form').show();
                $('#mpesa_mobile').show();
                $('#mobile').attr("required", "required");
                $('#payment_form').attr('action', "{!! url('client/saving/'.$saving->id.'/pay/mpesa') !!}");
            }
            if ($('#method').val() == 'stripe') {
                $('#payment_form').hide();
                $('#stripeForm').show();
            }
        });

        /* Visual feedback */
        var $form = $('#payment-form');
        $form.find('.subscribe').on('click', payWithStripe);

        /* If you're using Stripe for payments */
        function payWithStripe(e) {
            e.preventDefault();

            /* Abort if invalid form data */
            if (!validator.form()) {
                return;
            }

            /* Visual feedback */
            $form.find('.subscribe').html('Validating <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);

            var PublishableKey = '{{\App\Models\Setting::where('setting_key', 'stripe_publishable_key')->first()->setting_value}}'; // Replace with your API publishable key
            Stripe.setPublishableKey(PublishableKey);

            /* Create token */
            var expiry = $form.find('[name=cardExpiry]').payment('cardExpiryVal');
            var ccData = {
                number: $form.find('[name=cardNumber]').val().replace(/\s/g, ''),
                cvc: $form.find('[name=cardCVC]').val(),
                exp_month: expiry.month,
                exp_year: expiry.year
            };

            Stripe.card.createToken(ccData, function stripeResponseHandler(status, response) {
                if (response.error) {
                    /* Visual feedback */
                    $form.find('.subscribe').html('Try again').prop('disabled', false);
                    /* Show Stripe errors on the form */
                    $form.find('.payment-errors').text(response.error.message);
                    $form.find('.payment-errors').closest('.row').show();
                } else {
                    /* Visual feedback */
                    $form.find('.subscribe').html('Processing <i class="fa fa-spinner fa-pulse"></i>');
                    /* Hide Stripe errors on the form */
                    $form.find('.payment-errors').closest('.row').hide();
                    $form.find('.payment-errors').text("");
                    // response contains id and card, which contains additional card details
                    console.log(response.id);
                    console.log(response.card);
                    var token = response.id;
                    // AJAX - you would send 'token' to your server here.
                    $.post('{{url('client/saving/'.$saving->id.'/pay/stripe')}}', {
                        token: token,
                        _token: "{{csrf_token()}}",
                        amount: $('#stripe_amount').val()
                    })
                    // Assign handlers immediately after making the request,
                        .done(function (data, textStatus, jqXHR) {
                            $form.find('.subscribe').html('Payment successful <i class="fa fa-check"></i>');
                            window.location = "{{url('client/saving/'.$saving->id.'/show?msg=Payment successful')}}"
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            $form.find('.subscribe').html('There was a problem').removeClass('success').addClass('error');
                            /* Show Stripe errors on the form */
                            $form.find('.payment-errors').text('Try refreshing the page and trying again.');
                            $form.find('.payment-errors').closest('.row').show();
                        });
                }
            });
        }
        /* Fancy restrictive input formatting via jQuery.payment library*/
        $('input[name=cardNumber]').payment('formatCardNumber');
        $('input[name=cardCVC]').payment('formatCardCVC');
        $('input[name=cardExpiry]').payment('formatCardExpiry');

        /* Form validation using Stripe client-side validation helpers */
        jQuery.validator.addMethod("cardNumber", function (value, element) {
            return this.optional(element) || Stripe.card.validateCardNumber(value);
        }, "Please specify a valid credit card number.");

        jQuery.validator.addMethod("cardExpiry", function (value, element) {
            /* Parsing month/year uses jQuery.payment library */
            value = $.payment.cardExpiryVal(value);
            return this.optional(element) || Stripe.card.validateExpiry(value.month, value.year);
        }, "Invalid expiration date.");

        jQuery.validator.addMethod("cardCVC", function (value, element) {
            return this.optional(element) || Stripe.card.validateCVC(value);
        }, "Invalid CVC.");

        validator = $form.validate({
            rules: {
                cardNumber: {
                    required: true,
                    cardNumber: true
                },
                cardExpiry: {
                    required: true,
                    cardExpiry: true
                },
                cardCVC: {
                    required: true,
                    cardCVC: true
                }
            },
            highlight: function (element) {
                $(element).closest('.form-control').removeClass('success').addClass('error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-control').removeClass('error').addClass('success');
            },
            errorPlacement: function (error, element) {
                $(element).closest('.form-group').append(error);
            }
        });

        paymentFormReady = function () {
            if ($form.find('[name=cardNumber]').hasClass("success") &&
                $form.find('[name=cardExpiry]').hasClass("success") &&
                $form.find('[name=cardCVC]').val().length > 1) {
                return true;
            } else {
                return false;
            }
        }

        $form.find('.subscribe').prop('disabled', true);
        var readyInterval = setInterval(function () {
            if (paymentFormReady()) {
                $form.find('.subscribe').prop('disabled', false);
                clearInterval(readyInterval);
            }
        }, 250);
    </script>
@endsection
