<?php
namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Braintree\ClientToken;
use Braintree\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
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
			dd($result);
			flash('Transaction completed successfully!')->success();
		}elseif ($result->success == false) {
			// dd($result);
			flash($result->message)->error();
		}
		return redirect( '/subscription');
	}

	public function postPaymentWith2checkout(Request $request){

	}

	// other features you might be interested in

	public function join() {
		//check that we have nonce and plan in the incoming HTTP request
		if( empty( Input::get( 'payment_method_nonce' ) ) || empty( Input::get( 'plan' ) ) ){
			return redirect( '/subscription?success=false&message=' . urlencode( 'Invalid request' ), 400 );
		}
		//set user
		$user = Auth::user();
		try {
			//Try to create subscription
			$subscription = $user->newSubscription( 'main', Input::get( 'plan' ) )->create( Input::get( 'payment_method_nonce' ), [
				'email' => $user->email
			] );
		} catch ( \ Exception $e ) {
			//get message from caught error
			$message = $e->getMessage();
			//send back error message to view
			return redirect( '/subscription/join?success=false&message=' . urlencode( $message ) );
		}
		//Go to subscription manage view beacuse all is well
		return redirect( '/subscription/manage?success=true' );
		
	}

	public function cancel()
	{
		$user = Auth::user();
		$subscription =  $useruser->subscription('main')->cancel();
		return redirect( '/subscription/manage?success=true' );
	}
	public function manage()
	{
		$user = Auth::user();
		$subscriptions = $user->getSubscription();
		return view('subscription-manage', ['subscriptions' => $subscriptions, ]);
	}
}