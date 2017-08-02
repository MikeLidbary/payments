<?php
namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Braintree\ClientToken;
use Braintree\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Omnipay\Omnipay;
use Redirect;
use Session;
class SubscriptionController extends Controller
{
	public function index()
	{
		$clientToken = ClientToken::generate();
		return view('subscription.main', ['clientToken' => $clientToken ]);
	}

	public function checkout(Request $request){
		$user = Auth::user();
		$result = Transaction::sale([
		    'amount' => $request->input('amount'),
		    'paymentMethodNonce' => $request->input('payment_method_nonce'),
		    'options' => [
		        'submitForSettlement' => true
		    ]
		]);
		if ($result->success == true) {
			flash('Transaction completed successfully!')->success();
			return Redirect::route( 'payment.receipt' )
			->with( 'transaction_id',$result->transaction->id)
			->with( 'amount',$request->input('amount'));
			
		}elseif ($result->success == false) {
			flash($result->message)->error();
			return Redirect::route('maincheckout');
		}
		
	}

	public function postPaymentWith2checkout(Request $request){
		// dd($request->all());
		try {
		    $gateway = Omnipay::create('TwoCheckoutPlus_Token');
		    $gateway->setAccountNumber(env('2CHECKOUT_SELLER_ID'));
		    $gateway->setTestMode(true);
		    $gateway->setPrivateKey(env('2CHECKOUT_PRIVATE_KEY'));

		    $formData = array(
		        'firstName' => "fhhfhfhf",
		        'lastName' => "mfdfdjfjd",
		        'email' => "testingtester@2co.com",
		        'billingAddress1' => "123 Test ST",
		        'billingAddress2' => "485 HYJ GH",
		        'billingCity' => "Columbus",
		        'billingPostcode' => "458223",
		        'billingState' => "OH",
		        'billingCountry' => "USA",
		    );
		    $amount=(float) $request->input('amount');
		    $purchase_request_data = array(
		        'card' => $formData,
		        'token' => $request->input('token'),
		        'transactionId' => "123",
		        'currency' => 'USD',
		        'amount' => $amount,
		    );

		    $response = $gateway->purchase($purchase_request_data)->send();

		    if ($response->isSuccessful()) {
		        $transaction_ref = $response->getTransactionReference();
		        flash("Success")->success();
		        return Redirect::route( 'payment.receipt' )
				->with( 'transaction_id', $transaction_ref)
				->with( 'amount',$amount);
		    } else {
		        $error = $response->getMessage();
		        flash($error)->error();
		        return Redirect::route('maincheckout');
		    }
			} catch (Exception $e) {
			    $e->getMessage();
			    flash($e->getMessage())->error();
			    return Redirect::route('maincheckout');
			}
		}

		public function showReceipt(){
			$transaction_id =Session::get('transaction_id');
			$amount =Session::get('amount');
			if($transaction_id==""){
				return Redirect::route('maincheckout');
			}
			return view('subscription.receipt',compact('transaction_id','amount'));
		}

}