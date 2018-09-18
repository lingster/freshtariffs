@extends('layouts.master')
@section('title', 'Subscription')

@section('content')
    <h3>Subscription</h3>
    <p>
        At this page you can change view and change your subscription settings.<br />
        If you are looking for invoices, <a class="a-link" href="{{ URL::to('/staff/subscription/invoices') }}">click here</a>.
    </p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif
    <p>
        Your current plan:
        @if (($planType = $user->getSubscriptionType()) !== User::SUBSCRIPTION_TYPE_FREE)
            @if ($planType == User::SUBSCRIPTION_TYPE_SMALL)
                <strong style="color: #1080f2">SMALL TEAM</strong>
            @elseif ($planType == User::SUBSCRIPTION_TYPE_LARGE)
                <strong style="color: #ec5298">LARGE TEAM</strong>
            @endif
            <br />
            You can <a id="cancel-link" href="{{ URL::to('/staff/subscription/cancel') }}?_token={{ csrf_token() }}" onclick="return confirm('Are you sure?');" class="a-link">cancel your subscription</a>.
        @else
            <strong>STARTER</strong>
        @endif
    </p>
    <hr />
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <div class="pricing-table m-t-0-xs-max">
                <div class="pricing-table-header block-invert">
                    <h5 class="pricing-table-caption">Starter</h5>
                    <h2 class="pricing-table-title">FREE</h2>
                </div>
                <div class="pricing-table-content block-light">
                    <ul class="pricing-table-list">
                        <li><i class="fa fa-check"></i>1 user</li>
                        <li><i class="fa fa-check"></i>5 Customers</li>
                        <li><i class="fa fa-check"></i>Normal options</li>
                        <li><i class="fa fa-check"></i>Email support</li>
                    </ul>
                </div>
                <div class="pricing-table-footer block-light">
                    @if ($planType == User::SUBSCRIPTION_TYPE_FREE)
                        <button class="btn btn-primary" disabled>You are at this plan.</button>
                    @else
                        <button class="btn btn-primary" onclick="return subscribeModal(this, '{{ User::SUBSCRIPTION_TYPE_FREE }}')">Choose</button>
                    @endif
                </div>
            </div> <!-- .pricing-table -->
        </div>
        <div class="col-xs-12 col-sm-4">
            <div class="pricing-table">
                <div class="pricing-table-header block-primary">
                    <span class="pricing-table-badge">
                       <i class="fa fa-star"></i>
                                </span>
                    <h5 class="pricing-table-caption">Small Team</h5>
                    <h2 class="pricing-table-title"><span>$</span>49.99<span>/ mo.</span></h2>
                </div>
                <div class="pricing-table-content block-light">
                    <ul class="pricing-table-list">
                        <li><i class="fa fa-check"></i> Up to 3 users</li>
                        <li><i class="fa fa-check"></i> 25 customers</li>
                        <li><i class="fa fa-check"></i> Advanced options</li>
                        <li><i class="fa fa-check"></i> Email support</li>
                    </ul>
                </div>
                <div class="pricing-table-footer block-light">
                    @if ($planType == User::SUBSCRIPTION_TYPE_SMALL)
                        <button class="btn btn-primary" disabled>You are at this plan.</button>
                    @else
                        <button class="btn btn-primary" onclick="return subscribeModal(this, '{{ User::SUBSCRIPTION_TYPE_SMALL }}')">Choose</button>
                    @endif
                </div>
            </div> <!-- .pricing-table -->
        </div>
        <div class="col-xs-12 col-sm-4">
            <div class="pricing-table">
                <div class="pricing-table-header block-pink">
                    <h5 class="pricing-table-caption">Large Team</h5>
                    <h2 class="pricing-table-title"><span>$</span>399<span>/ mo.</span></h2>
                </div>
                <div class="pricing-table-content block-light">
                    <ul class="pricing-table-list">
                        <li><i class="fa fa-user-plus"></i> Unlimited users</li>
                        <li><i class="fa fa-envelope"></i> <span class="color-pink">Unlimited</span> Customers</li>
                        <li><i class="fa fa-cog"></i> Advanced reports</li>
                        <li><i class="fa fa-support"></i> Phone support</li>
                    </ul>
                </div>
                <div class="pricing-table-footer block-light">
                    @if ($planType == User::SUBSCRIPTION_TYPE_LARGE)
                        <button class="btn btn-primary" disabled>You are at this plan.</button>
                    @else
                        <button class="btn btn-primary" onclick="return subscribeModal(this, '{{ User::SUBSCRIPTION_TYPE_LARGE }}')">Choose</button>
                    @endif
                </div>
            </div> <!-- .pricing-table -->
        </div>
    </div>
    <hr />

    <div class="modal fade" id="subscribeModal" tabindex="-1" role="dialog" aria-labelledby="subscribeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="subscribeModalLabel">Subscribing</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Please fill in your card data. We do not store it anywhere, all payments is handled by <a href="https://www.stripe.com">Stripe</a>.
                    </div>
                    <hr />
                    <div class="well well-sm">
                        <img class="pull-right" style="margin-top: -8px; clip-path: inset(10px 20px 30px 40px)" src="http://i76.imgup.net/accepted_c22e0.png">
                        <strong>Payment Details</strong>
                    </div>

                    <div class="alert alert-danger" id="subscribeModalError" style="display: none"></div>

                    <div class="row">
                        <div class="col-xs-12 col-md-8 col-md-offset-2">
                            <form role="form" id="paymentForm">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="cardHolderName">Name on Card</label>
                                            <input type="text" class="form-control" name="nameoncard" id="nameoncard" placeholder="Cardholder Name" autocomplete="cc-name" required data-stripe="name" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="cardNumber">Credit Card Number</label>
                                            <input type="text" class="form-control" name="ccnumber" id="ccnumber" placeholder="Valid Card Number" autocomplete="cc-number" required data-stripe="number" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-7 col-md-9">
                                        <div class="form-group">
                                            <label for="expMonth">EXPIRATION DATE</label>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control" id="cc-exp-month" name="cc-exp-month" autocomplete="cc-exp-month" placeholder="MM" required data-stripe="exp_month" />
                                                </div>
                                                <div class="col-xs-6 col-lg-6" style="padding-left: 0">
                                                    <input type="text" class="form-control" id="cc-exp-year" name="cc-exp-year" autocomplete="cc-exp-year" placeholder="YY" required data-stripe="exp_year" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-5 col-md-3 pull-right">
                                        <div class="form-group">
                                            <label for="cvCode">CVV</label>
                                            <input type="text" class="form-control" id="cvv2" name="cvv2" autocomplete="cc-csc" placeholder="CVV" required data-stripe="cvc" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-pink btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" onclick="return subscribeModalSubmit();" class="btn btn-primary btn-sm" id="subscribeModalBtn">Start subscription</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">
        Stripe.setPublishableKey('{{ Config::get('services.stripe.public') }}');
        var paymentForm = $('#paymentForm');
        var subscribeModalError = $('#subscribeModalError');
        var subscribeModalBtn = $('#subscribeModalBtn');
        var _selectedPlan = '';
        var currentPlan = '{{ $planType }}';

        function subscribeModal(el, plan) {
            _selectedPlan = plan;
            if (currentPlan !== '{{ User::SUBSCRIPTION_TYPE_FREE }}') {
                if(!confirm("Are you sure?")) return;
                var button = $(el);
                button.prop('disabled', true);
                button.html('Processing...');
                $.post('/api/subscription/swap?_token={{ csrf_token() }}', {
                    'plan':  _selectedPlan
                }, function (apiResponse) {
                    button.prop('disabled', false);
                    button.html('Choose');
                    window.location.reload();
                });
                return;
            }
            subscribeModalError.hide();
            $('#subscribeModal').modal();
        }

        function subscribeModalHandleError(response) {
            subscribeModalError.text(response.error.message);
            subscribeModalError.fadeIn();
            subscribeModalBtn.prop('disabled', false);
            subscribeModalBtn.html('Start subscription');
        }

        function subscribeModalSubmit() {
            subscribeModalBtn.prop('disabled', true);
            subscribeModalBtn.html('Processing...');
            Stripe.card.createToken(paymentForm, function(status, response) {
                if (response.error) {
                    subscribeModalHandleError(response);
                    return;
                }

                var token = response.id;
                $.post('/api/subscription?_token={{ csrf_token() }}', {
                    'plan': _selectedPlan,
                    'token': token
                }, function (apiResponse) {
                    if (apiResponse.status === 'ok') {
                        window.location.reload();
                    } else {
                        subscribeModalHandleError(apiResponse);
                    }
                });
            });

            return false;
        }
    </script>
@endsection