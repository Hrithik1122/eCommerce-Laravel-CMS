<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use Redirect;
use App\Model\Order;
use App\Model\Review;
use App\Model\Paymentlogs;
use App\Model\Shipping;
use App\Model\OrderResponse;
use App\Model\OrderData;
use App\Model\Setting;
use App\Model\Product;
use App\Model\Taxes;
use App\Model\PaymentMethod;
use App\User;
use URL;
use Mail;
use Image;
use PDF;
use Hash;
use DateTimeZone;
use DateTime;
use DB;
use Cart;
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
use Auth;
class PaypalController extends Controller {
  
   private $_api_context;

      public function __construct()
      {
        parent::callschedule();
        $setting=PaymentMethod::find(1);
        $paypal_conf = \Config::get('paypal');
        if($setting->payment_mode==1){
           $mode="sandbox";
        }
        else{
          $mode="live";
        }
        $this->_api_context = new ApiContext(new OAuthTokenCredential($setting->payment_key,$setting->payment_secret));
        $this->_api_context->setConfig(array('mode' =>$mode,'http.ConnectionTimeOut' => 1000,'log.LogEnabled' => true,'log.FileName' => storage_path() . '/logs/paypal.log','log.LogLevel' => 'FINE'));
    }

    public function postPaymentWithpaypal(Request $request){
         $setting=Setting::find(1);
                   $shipping=Shipping::all();
                  $cartCollection = Cart::getContent();
                  $gettimezone=$this->gettimezonename($setting->default_timezone);
                  date_default_timezone_set($gettimezone);
                  $input = $request->input();
                   $getjson=$this->getorderjson();
                      if($request->get("couponcode")){
                            $datacoupoun=$this->verifiedcoupon($request->get("couponcode"));
                            if($datacoupoun->discount_type==1){
                                $coupon_price=(Cart::gettotal()*$datacoupoun->value)/100;
                            }else{
                                $coupon_price=$datacoupoun->value;
                            }
                           
                            $charges=0;
                            if($datacoupoun->free_shipping==1){
                                $shipping_charge="0.00";
                            }else{
                                    if($request->get("shipping_type")==1){
                                        $charges=$shipping[0]->cost;
                                        $shipping_charge=$shipping[0]->cost;
                                    }else{
                                        $charges=$shipping[1]->cost;
                                        $shipping_charge=$shipping[1]->cost;
                                    }
                            }
                            $is_freeshipping=$datacoupoun->value;
                           
                           // $coupon_price=$coupon_price;
                        }else{
                            $is_freeshipping=0;
                            if($request->get("shipping_type")==1){
                                $charges=$shipping[0]->cost;
                                $shipping_charge=$shipping[0]->cost;
                            }else{
                                $charges=$shipping[1]->cost;
                                $shipping_charge=$shipping[1]->cost;
                            }
                           
                            $coupon_price=0;
                            
                        }
                        
                        $total=Cart::gettotal()+$getjson["total"]+$charges-$coupon_price;
        $cartCollection = Cart::getContent();
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();

        $item_1->setName(__('messages.site_name')) 
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(number_format($total, 2, '.', ''));

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal(number_format($total, 2, '.', ''));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription(__('messages.Your transaction description'));

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('status'))
            ->setCancelUrl(URL::route('status'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        try {
               $payment->create($this->_api_context);
           
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {                
                Session::flash('message', __('messages_error_success.connection_timeout')); 
                Session::flash('alert-class', 'alert-danger');
                return Redirect::route('checkout');
              
            } else {
                Session::flash('message', __('messages_error_success.paypal_error_1')); 
                Session::flash('alert-class', 'alert-danger');
                return Redirect::route('checkout');
                
            }
        }
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                 
                       
                  DB::beginTransaction();
                   try {
                        $store=new Order();
                        $store->user_id=Auth::id();
                        $store->orderdate=date("d-m-Y h:i:s");
                        $store->shipping_method=$request->get("shipping_method");
                        $store->payment_method=$request->get("payment_method");
                        $store->billing_first_name=$request->get("order_firstname");
                        $store->billing_address=$request->get("order_billing_address");
                        $store->billing_city=$request->get("order_billing_city");
                        $store->billing_pincode=$request->get("order_billing_pincode");
                        $store->phone=$request->get("order_phone");
                        $store->email=$request->get("order_email");
                        $store->to_ship=$request->get("to_ship");
                        $store->notes=$request->get("order_notes");
                        $store->shipping_city=$request->get("order_shipping_city");
                        $store->shipping_pincode=$request->get("order_shipping_pincode");
                        $store->shipping_first_name=$request->get("order_ship_firstname");
                        $store->shipping_address=$request->get("order_shipping_address");
                        $store->subtotal=number_format(Cart::gettotal(), 2, '.', '');
                        $store->shipping_method=$request->get("shipping_type");
                        $store->is_freeshipping=$is_freeshipping;
                        $store->shipping_charge=$shipping_charge;
                        $store->coupon_code=$request->get("couponcode");
                        $store->paypal_payment_Id=$payment->getId();
                        $store->coupon_price=$coupon_price;
                        $store->order_status='3';
                        $store->taxes_charge=number_format($getjson["total"], 2, '.', '');;
                         $store->total=number_format($total, 2, '.', '');
                        $store->save();
                        $storeres=new OrderResponse();
                        $storeres->order_id=$store->id;
                        $storeres->desc=json_encode($this->getorderjson());
                        $storeres->save();
                        $jsondata=$this->getorderjson();
        
                  foreach($jsondata["order"] as $k) {
                      $add=new OrderData();
                      $add->order_id=$store->id;
                      $add->product_id=$k["ProductId"];
                      $add->quantity=$k["ProductQty"];
                      $add->price=$k["ProductAmt"];
                      $add->total_amount=$k["ProductTotal"];
                      $add->tax_charges=$k["tax_amount"];
                      $add->tax_name=$k["tax_name"];
                      $add->option_name=$k["exterdata"]["option"];
                      $add->label=$k["exterdata"]["label"];
                      $add->option_price=$k["exterdata"]["price"];
                      $add->save();
                  }
                  
                   
                  
                 DB::commit();
                 Session::put("order_id",$store->id);
                  
                 } catch (\Exception $e) {
                     DB::rollback();
                    
        return Redirect::route('checkout');
                }
                break;
            }
        }

        Session::put('paypal_payment_id', $payment->getId());

        if(isset($redirect_url)) {
            return Redirect::away($redirect_url);
        }
         $order=Order::where("paypal_payment_Id",$payment_id)->first();
         if($order){
            $order->delete();
         }
        Session::flash('message', __('messages_error_success.payment_fail')); 
        Session::flash('alert-class', 'alert-danger');
        return Redirect::route('checkout');
    }

    public function getPaymentStatus(Request $request)
    {
        $payment_id = Session::get('paypal_payment_id');
        if (empty($request->get('PayerID')) || empty($request->get('token'))) {
            \Session::put('error','Payment failed');
             $order=Order::where("paypal_payment_Id",$payment_id)->first();
             if($order){
                $order->delete();
             }
            Session::flash('message', __('messages_error_success.payment_fail')); 
        Session::flash('alert-class', 'alert-danger');
        return Redirect::route('checkout');
        }
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') { 
             $order=Order::where("paypal_payment_Id",$payment_id)->first();
             $order->paypal_token=$request->get('token');
             $order->paypal_payer_ID=$request->get('PayerID');
             $order->save();
             Cart::clear();
             Session::flash('message', __('messages_error_success.order_place_success')); 
             Session::flash('alert-class', 'alert-danger');
             return redirect("vieworder/".$order->id);
        }
         $order=Order::where("paypal_payment_Id",$payment_id)->first();
         if($order){
            $order->delete();
         }
        Session::flash('message', __('messages_error_success.payment_fail')); 
        Session::flash('alert-class', 'alert-danger');
        return Redirect::route('checkout');
    }

     public function getorderjson(){
      $cartCollection = Cart::getContent();
      $total=0;
      $main_array=array();  
        foreach ($cartCollection as $item) {
           $order=array();
           $gettotal=array();
           $subtotal=$item->price*$item->quantity;
           $producttax=Product::where("name",$item->name)->first();
           $taxdata=Taxes::find($producttax->tax_class);
           $a=$taxdata->rate/100;
           $b=$subtotal*$a;
           $order["ProductId"]=$producttax->id;
           $order["ProductQty"]=$item->quantity;
           $order["ProductAmt"]=$item->price;
           $order["ProductTotal"]=$item->price*$item->quantity;
           $order["tax_name"]=$taxdata->tax_name;
           $order["tax_amount"]=number_format((float)$b, 2, '.', '');
           $order["exterdata"]=$item->attributes[0];
           $main_array[]=$order;
           $total=$total+$b;
        }
     return array("order"=>$main_array,"total"=>$total);
   }
 
     static public function generate_timezone_list(){
          static $regions = array(
                     DateTimeZone::AFRICA,
                     DateTimeZone::AMERICA,
                     DateTimeZone::ANTARCTICA,
                     DateTimeZone::ASIA,
                     DateTimeZone::ATLANTIC,
                     DateTimeZone::AUSTRALIA,
                     DateTimeZone::EUROPE,
                     DateTimeZone::INDIAN,
                     DateTimeZone::PACIFIC,
                 );
                  $timezones = array();
                  foreach($regions as $region) {
                            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
                  }

                  $timezone_offsets = array();
                  foreach($timezones as $timezone) {
                       $tz = new DateTimeZone($timezone);
                       $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
                  }
                 asort($timezone_offsets);
                 $timezone_list = array();
    
                 foreach($timezone_offsets as $timezone=>$offset){
                          $offset_prefix = $offset < 0 ? '-' : '+';
                          $offset_formatted = gmdate('H:i', abs($offset));
                          $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
                          $timezone_list[] = "$timezone";
                 }

                 return $timezone_list;
                ob_end_flush();
       }

       public function gettimezonename($timezone_id){
              $getall=$this->generate_timezone_list();
              foreach ($getall as $k=>$val) {
                 if($k==$timezone_id){
                     return $val;
                 }
              }
       }
}