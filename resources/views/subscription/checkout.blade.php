@extends('layouts.app')
@section('content')
<form id="checkout" action="{{ url('subscription/checkout') }}" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="">Choose option:</label>
            <select name="plan" id="plan" class="form-control">
                <option value="small">Small ($5/month)</option>
                <option value="big">Big ($10/month)</option>
            </select>
        </div>
        <div class="form-group">
            <div id="number" class="form-control"></div>
        </div>
        <div class="form-group">
            <div id="expiration-date" class="form-control"></div>
        </div>
        <div class="form-group">
            <div id="cvv" class="form-control"></div>
        </div>
        <input type="submit" id="submit" value="Pay">
</form>
@endsection
@section('scripts')
    <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
    <script>
        var colorTransition = 'color 100ms ease-out';

        braintree.setup("{{ $clientToken }}", "custom", {
            id: "checkout",
            hostedFields: {
                styles: {
                    // Style all elements
                    "input": {
                        "color": "#333"
                    },

                    // Styling element state
                    ":focus": {
                        "color": "blue"
                    },
                    ".valid": {
                        "color": "green"
                    },
                    ".invalid": {
                        "color": "red"
                    },

                    // Media queries
                    // Note that these apply to the iframe, not the root window.
                    "@media screen and (max-width: 700px)": {
                        "input": {}
                    }
                },
                number: {
                    selector: "#number"
                },
                expirationDate: {
                    selector: "#expiration-date"
                },
                cvv: {
                    selector: "#cvv"
                }
            }
        });
    </script>
    @endsection