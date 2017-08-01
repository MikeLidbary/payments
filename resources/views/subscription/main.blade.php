@extends('layouts.app')
@section('styles')
<link href="/css/style.css" rel="stylesheet">
@endsection
@section('content')
<div class="demo-frame">  

<!-- Braintree Begins -->
  <form action="{{ url('subscription/checkout') }}" method="post" id="cardForm" >
  {{ csrf_field() }}
    <label class="hosted-fields--label" for="amount">Amount</label>
    <input type="text" id="amount" name="amount" class="hosted-field"/>

    <label class="hosted-fields--label" for="card-number">Card Number</label>
    <div id="card-number" class="hosted-field"></div>

    <label class="hosted-fields--label" for="expiration-date">Expiration Date</label>
    <div id="expiration-date" class="hosted-field"></div>

    <label class="hosted-fields--label" for="cvv">CVV</label>
    <div id="cvv" class="hosted-field"></div>

    <label class="hosted-fields--label" for="postal-code">Postal Code</label>
    <div id="postal-code" class="hosted-field"></div>

    <div class="button-container">
    <input type="submit" class="button button--green" value="Paywith Braintree" id="submit"/>
    </div>
  </form>
<!-- Braintree Ends -->

<!-- Paypal Begins -->
<form class="form-horizontal" method="POST" id="payment-form" role="form" action="{!! URL::route('addmoney.paypal') !!}" >
{{ csrf_field() }}
<input id="amount" type="hidden" class="form-control" name="amount" value="10">
<div class="form-group">
    <div class="col-md-6 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            Paywith Paypal
        </button>
    </div>
</div>
</form>
<!-- Paypal Ends -->

<!-- 2checkout form begins -->
<form id="tcoCCForm" action="https://www.mysite.com/examplescript.php" onsubmit="return false" method="post">
  <input id="sellerId" type="hidden" value="1817037" />
  <input id="publishableKey" type="hidden" value="087F9356-39A3-4CEC-AAEE-0694E4B619EE" />
  <input id="token" name="token" type="hidden" value="" />
  <div>
    <label>
      <span>Card Number</span>
      <input id="ccNo" type="text" value=""/>
    </label>
  </div>
  <div>
    <label>
      <span>Expiration Date (MM/YYYY)</span>
      <input type="text" size="2" id="expMonth" />
    </label>
    <span> / </span>
    <input type="text" size="4" id="expYear" />
  </div>
  <div>
    <label>
      <span>CVC</span>
      <input id="cvv" type="text" value=""/>
    </label>
  </div>
  <input type="submit" value="Submit Payment" onclick="retrieveToken()" />
</form>
<!-- 2checkout form ends -->
<div>
@endsection
@section('scripts')
    <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
    <script>
        var colorTransition = 'color 100ms ease-out';

        braintree.setup("{{ $clientToken }}", "custom", {
            id: "cardForm",
            hostedFields: {
                styles: {
                      'input': {
                        'font-size': '16px',
                        'font-family': 'courier, monospace',
                        'font-weight': 'lighter',
                        'color': '#ccc'
                      },
                      ':focus': {
                        'color': 'black'
                      },
                      '.valid': {
                        'color': '#8bdda8'
                      },                  
                     '.invalid': {
                        'color': 'red'
                        },
                    '@media screen and (max-width: 700px)': {
                        'input': {}
                    }
                },
                number: {
                    selector: "#card-number",
                    placeholder: '4111 1111 1111 1111'
                },
                expirationDate: {
                    selector: "#expiration-date",
                    placeholder: 'MM/YYYY'
                },
                cvv: {
                    selector: "#cvv",
                    placeholder: '123'
                },      
                postalCode: {
                    selector: '#postal-code',
                    placeholder: '11111'
                }
            }
        });
    </script>
    @endsection