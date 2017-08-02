@extends('layouts.app')
@section('styles')
<link href="/css/style.css" rel="stylesheet">
@endsection
@section('content')
<div class="demo-frame">  
    <div id="myRadioGroup">
        PayWith Braintree  <input type="radio" name="gateways" checked="checked" value="2"  />
        Paywith Paypal  <input type="radio" name="gateways" value="3" />
        Paywith 2checkout  <input type="radio" name="gateways" value="4" />

        <div id="Gateways2" class="desc">
            <!-- Braintree Begins -->
              <form action="{!! URL::route('addmoney.braintree') !!}" method="post" id="cardForm" >
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
                <input type="submit" class="btn btn-success" value="Paywith Braintree" id="submit"/>
                </div>
              </form>
            <!-- Braintree Ends -->
        </div>
        <div id="Gateways3" class="desc" style="display: none;">
            <!-- Paypal Begins -->
            <br>
                <form  method="POST" id="payment-form" role="form" action="{!! URL::route('addmoney.paypal') !!}" id="paypalForm" >
                {{ csrf_field() }}
                <label class="hosted-fields--label" for="amount">Amount</label>
                <input type="text" id="paypalAmount" name="amount" class="hosted-field"/>
                <div class="button-container">
                    <button type="submit" class="btn btn-primary">
                        Paywith Paypal
                    </button>
                </div>
                </form>
            <!-- Paypal Ends -->
        </div>
        <div id="Gateways4" class="desc" style="display: none;">
            <!-- 2checkout form begins -->
                <form id="tcoCCForm" action="{!! URL::route('addmoney.2checkout') !!}" onsubmit="return false" method="post">
                {{ csrf_field() }}
                  <label class="hosted-fields--label" for="amount">Amount</label>
                  <input type="text"  name="amount" class="hosted-field"/>
                  <input id="sellerId" type="hidden" value="{{env('2CHECKOUT_SELLER_ID')}}" />
                  <input id="publishableKey" type="hidden" value="{{env('2CHECKOUT_PUBLISHABLE_KEY')}}" />
                  <input id="token" name="token" type="hidden" value="" />
                  <label class="hosted-fields--label" for="amount">Card Number</label>
                  <input type="text" id="ccNo" class="hosted-field" required="required" />
  <!--                 <div>
                    <label>
                      <input id="ccNo" type="hidden" value=""/>
                    </label>
                  </div> -->
                  <label class="hosted-fields--label">Expiry Month</label>
                  <input type="text" id="expMonth" class="hosted-field" placeholder="MM"  required="required" />
                  <label class="hosted-fields--label" >Expiry Year</label>
                  <input type="text" id="expYear" class="hosted-field" placeholder="YYYY"  required="required"  />
<!--                   <div>
                    <label>
                      <input type="hidden" size="2" id="expMonth" value="" />
                    </label>
                    <input type="hidden" size="4" id="expYear"  value=""/>
                  </div> -->
                  <label class="hosted-fields--label">CVV</label>
                  <input type="text" id="cVV" class="hosted-field" placeholder="123"   required="required"  />
<!--                   <div>
                    <label>
                      <input id="cVV" type="hidden" value=""/>
                    </label>
                  </div> -->
                  <div class="button-container">
                    <input type="submit" class="btn btn-info" value="PayWith 2Checkout" />
                  </div>
                </form>
            <!-- 2checkout form ends -->
        </div>
    </div>
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
    <script src="https://www.2checkout.com/checkout/api/2co.min.js"></script>
<!--     <script>
          function successCallback(data) {
            var myForm = document.getElementById('tcoCCForm');
            myForm.token.value = data.response.token.token;
            myForm.submit();        
          }

          function errorCallback(data) {
            alert(data.errorMsg);           
          }

          function retrieveToken() {
            var datar = $('#expiration-date').text();
            var arr = datar.split('/');
            var args = {
                sellerId: $("#sellerId").val(),
                publishableKey: $("#publishableKey").val(),
                ccNo: $("#card-number").val(),
                cvv: $("#cvv").val(),
                expMonth: arr[0],
                expYear: arr[1]
            };
            TCO.requestToken(successCallback, errorCallback, args);
          }
    </script> -->
    <script type="text/javascript">

  // Called when token created successfully.
  var successCallback = function(data) {
    var myForm = document.getElementById('tcoCCForm');

    // Set the token as the value for the token input
    myForm.token.value = data.response.token.token;

    // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
    myForm.submit();
  };

  // Called when token creation fails.
  var errorCallback = function(data) {
    if (data.errorCode === 200) {
      // This error code indicates that the ajax call failed. We recommend that you retry the token request.
    } else {
      alert(data.errorMsg);
    }
  };

  var tokenRequest = function() {
    // Setup token request arguments
    var args = {
        sellerId: {{env('2CHECKOUT_SELLER_ID')}},
        publishableKey: "{{env('2CHECKOUT_PUBLISHABLE_KEY')}}",
        ccNo: $("#ccNo").val(),
        cvv: $("#cVV").val(),
        expMonth: $("#expMonth").val(),
        expYear: $("#expYear").val()
    };
    // Make the token request
    TCO.requestToken(successCallback, errorCallback, args);
  };

  $(function() {
    // Pull in the public encryption key for our environment
    TCO.loadPubKey('sandbox');

    $("#tcoCCForm").submit(function(e) {
      // Call our token request function
      tokenRequest();

      // Prevent form from submitting
      return false;
    });
  });

</script>
<!-- Hide/Show Gateway available -->
<script type="text/javascript">
    $(document).ready(function() {
    $("input[name$='gateways']").click(function() {
        var test = $(this).val();

        $("div.desc").hide();
        $("#Gateways" + test).show();
    });
});
</script>
    @endsection