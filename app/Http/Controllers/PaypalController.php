<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
/** All Paypal Details class **/
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

class PaypalController extends Controller
{
    private $_api_context;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        /** setup PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPaymentWithpaypal(Request $request)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName('Item 1') /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($request->get('amount')); /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($request->get('amount'));
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('payment.status')) /** Specify return URL **/
            ->setCancelUrl(URL::route('payment.status'));
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
            	flash("Connection timeout")->error();
                return Redirect::route('maincheckout');
            } else {
                flash("Sorry, some error occurred, please try again")->error();
                return Redirect::route('maincheckout');
            }
        }
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());
        if(isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        flash("Unknown error occurred")->error();
        return Redirect::route('maincheckout');
    }

    public function getPaymentStatus(Request $request)
    {

        $payment_id = $request->input('paymentId');
        if (empty($request->input('PayerID')) || empty($request->input('token'))) {
        	flash("Payment Failed")->error();
            return Redirect::route('maincheckout');
        }
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->input('PayerID'));
        
        try {
			$result = $payment->execute($execution, $this->_api_context);
		} catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
            	flash("Connection timeout")->error();
                return Redirect::route('maincheckout');
            } else {
                flash("Sorry, some error occurred, please try again")->error();
                return Redirect::route('maincheckout');
            };
		}catch (\PayPal\Exception\PayPalConnectionException $ex) {
		    flash("Sorry, transaction failed")->error();
		    return Redirect::route('maincheckout');
		} catch (Exception $ex) {
		    flash("Unknown error occurred")->error();
		    return Redirect::route('maincheckout');
		}
        if ($result->getState() == 'approved') { 
            flash("Payment Success")->success();
            return Redirect::route( 'payment.receipt' )
			->with( 'transaction_id',$payment_id)
			->with( 'amount',$result->transactions[0]->amount->total);
        }

        flash("Payment Failed")->error();
        	return Redirect::route('maincheckout');
    	}
  }
