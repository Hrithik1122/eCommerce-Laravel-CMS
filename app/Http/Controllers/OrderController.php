<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Queue\SerializesModels;
use Sentinel;
use Session;
use DataTables;
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
use App\Model\Token;
use App\User;
use Mail;
use Image;
use Config;
use PDF;
use Hash;
use DateTimeZone;
use DateTime;
use DB;
use Cart;
use Auth;
use Redirect;

class OrderController extends Controller {
     use SerializesModels;
   public function __construct() {
         parent::callschedule();
    }
  public function showorder(){
      return view("admin.order.default");
  }

  public function orderdatatable(){
         $order =Order::orderBy('id','DESC')->get();
         return DataTables::of($order)
            ->editColumn('id', function ($order) {
                return $order->id;
            })
            ->editColumn('name', function ($order) {
                   $data=User::find($order->user_id);
                 if($data){
                   return $data->first_name;
                 }
                 else{
                    return "";
                 }
            })
            ->editColumn('shipping_method', function ($order) {
                 $data=Shipping::find($order->shipping_method);
                 if($data){
                    return $data->label;
                 }
                 return "";
            })
             ->editColumn('payment_method', function ($order) {
                 if($order->payment_method=="1"){
                    return __('messages.paypal');
                 }elseif($order->payment_method=="2"){
                    return __('messages.stripe');
                 }else{
                    return __('messages.case_on_delivery');
                 }
                 return $order->payment_method;
            })
         
            ->editColumn('total', function ($order) {
                 $setting=Setting::find(1);
                 $getcurrency=explode("-",$setting->default_currency);
                 return $getcurrency[1].$order->total;
            })
             ->editColumn('view', function ($order) {                 
                 return $order->id;
            })
             
            ->editColumn('action', function ($order) { 
                 
                 $return = '<select name="status" class="form-control" onchange="savestatusorder('.$order->id.',this.value)">';
                 if($order->order_status=='6'){
                   $return=$return.'<option value="6" selected>'.__("messages.canceled").'</option>';
                   if($order->payment_method!='3'){
                      $return=$return.'<option value="7">'.__("messages.refunded").'</option>';
                   }
                 }else if($order->order_status=='5'){
                   $return=$return.'<option value="5" selected>'.__("messages.completed").'</option>';
                 }else if($order->order_status=='2'){
                   $return=$return.'<option value="2" selected>'.__("messages.on_hold").'</option><option value="4">'.__("messages.out_of_delivery").'</option><option value="6">'.__("messages.canceled").'</option>';
                 }else if($order->order_status=='3'){
                  $return=$return.'<option value="3" selected>'.__("messages.pending").'</option><option value="1">'.__("messages.processing").'</option><option value="6">'.__("messages.canceled").'</option>';
                }else if($order->order_status=='1'){
                  $return=$return.'<option value="1" selected>'.__("messages.processing").'</option><option value="2">'.__("messages.on_hold").'</option><option value="6">'.__("messages.canceled").'</option>';
                }else if($order->order_status=='7'&&$order->payment_method!='3'){
                  $return=$return.'<option value="7" selected>'.__("messages.refunded").'</option>';
                }
                else if($order->order_status=='4'){
                  $return=$return.'<option value="4" selected>'.__("messages.out_of_delivery").'</option><option value="5">'.__("messages.completed").'</option>';
                }

                       
                 return $return;              
            })           
            ->make(true);
  }

  public function vieworder($id){    
     $data=Order::find($id);
     $user=User::find($data->user_id);
     $shipping=Shipping::find($data->shipping_method);
     $order_data=OrderData::with("productdata")->where("order_id",$id)->get(); 
     $generatepdf=$this->generateorderpdf($id);
      $setting=Setting::find(1);
        $res_curr=explode("-",$setting->default_currency); 
     return view("admin.order.vieworder")->with("order",$data)->with("orderdata",$order_data)->with("user",$user)->with("shipping",$shipping)->with('pdfname',$generatepdf)->with("currency",$res_curr[1]);
  }

  public function generateorderpdf($id){
     $setting=Setting::find(1);
     $order=Order::find($id);
      $res_curr=explode("-",$setting->default_currency);
     $order_data=OrderData::with("productdata")->where("order_id",$id)->get(); 
      $html='<style type="text/css">*{font-family: Verdana, Arial, sans-serif;}table{
        font-size: x-small;}tfoot tr td{font-weight: bold;font-size: x-small;}.gray {
        background-color: lightgray}</style><table width="100%"><tr>
        <td valign="top"><img src="'.asset('public/Ecommerce/images/')."/".$setting->logo.'" alt="" width="150"/></td>
        <td align="right" style="width:60%">';
        $html=$html.'<h3>'.$setting->company_name.'</h3>';
          $html=$html.$setting->address.'<pre></pre>'.$setting->email.'<pre></pre>'.$setting->phone.'</pre>     
            </pre>
        </td>
    </tr>

  </table>

  <table width="100%">
    <tr>
        <td><strong>'.__("messages.billing_address").':</strong> 
            <pre></pre>'.$order->billing_first_name.'<pre></pre>
            '.$order->billing_address.'<pre></pre>
        </td>
        <td><strong>'.__("messages.shipping_address").':</strong>
             <pre></pre>'.$order->billing_first_name.'<pre></pre>
             '.$order->shipping_address.'<pre></pre>
        </td>
    </tr>

  </table>

  <br/>

  <table width="100%">
    <thead style="border-bottom: 1px solid lightgray;">
      <tr>
         <th>'.__("messages.product").'</th>
         <th>'.__("messages.unit_price").'</th>
         <th>'.__("messages.qty").'</th>
         <th>'.__("messages.line_total").'</th>
      </tr>
    </thead>
    <tbody style="border-bottom: 1px solid lightgray;">
    ';
      foreach ($order_data as $od) {
          $html=$html.'<tr><td ><strong>'.substr($od->productdata->name,0,15).'</strong>';
           if($od->option_name!=""&&$od->option_name!="null"){
                $opna=explode(",",$od->option_name);
                $label=explode(",",$od->label);
                for($i=0;$i<count($opna);$i++){
                    $html=$html.'<pre></pre><span style="font-size: small;">'.$opna[$i].'=>'.$label[$i].'</span>';
                }  
            }
          $html=$html.'</td><td style="text-align:center">'.$res_curr[1].number_format((float)$od->price, 2, '.', '');
          if($od->option_name!=""&&$od->option_name!="null"){
                                              $price=explode(",",$od->option_price);
                                              $label=explode(",",$od->label);
                                              for($i=0;$i<count($opna);$i++){
                                                  $t=0;
                                                  if(isset($price[$i])&&$price[$i]!=""&&$price[$i]!="null"){
                                                      $t=trim($price[$i]);
                                                  }else{
                                                      $t=0.00;
                                                  }
                                                  if(empty($t)){
                                                      $html=$html."<pre></pre>".$res_curr[1]."0.00";
                                                  }else{
                                                      $html=$html."<pre></pre>".$res_curr[1].$t;
                                                  }
                                                  
                                              }
                                          }
          $html=$html.'</td><td style="text-align:center">'.$od->quantity.'</td><td style="text-align:center">'.$res_curr[1].number_format((float)$od->total_amount, 2, '.', '').'</td></tr>';
      }
    
     $html=$html.'
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"></td>
            <td align="center">'.__("messages.subtotal").'</td>
            <td align="center">'.$res_curr[1].number_format((float)$order->subtotal, 2, '.', '').'</td>
        </tr>        
        <tr>
            <td colspan="2"></td>
            <td align="center">'.__("messages.shipping").'</td>
            <td align="center">'.$res_curr[1].number_format((float)$order->shipping_charge, 2, '.', '').'</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td align="center">'.__("messages.taxes").'</td>
            <td align="center">'.$res_curr[1].number_format((float)$order->taxes_charge, 2, '.', '').'</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td align="center" style="border-top: 1px solid lightgray;">Total</td>
            <td align="center" class="" style="border-top: 1px solid lightgray;">'.$res_curr[1].number_format((float)$order->total, 2, '.', '').'</td>
        </tr>
    </tfoot>
  </table>
';
      $file_name=$this->getName();
      $pdf=PDF::loadHTML($html);
      $pdf->setPaper('a4', 'landscape');
      $pdf->setWarnings(false);
      $pdf->save(public_path('pdf/'.$file_name));
      return $file_name;

  }

  public function latestorder(){
       $order =Order::orderBy('id','DESC')->take(10)->get();
         return DataTables::of($order)
            ->editColumn('id', function ($order) {
                return $order->id;
            })
            ->editColumn('customer', function ($order) {
                 $data=User::find($order->user_id);
                 if($data){
                   return $data->first_name;
                 }
                 else{
                    return "";
                 }
                
            })
            ->editColumn('status', function ($order) {
                 if($order->order_status=='6'){

                   return __("messages.canceled");

                 }else if($order->order_status=='5'){

                   return __("messages.completed");

                 }else if($order->order_status=='2'){

                   return __("messages.on_hold");

                 }else if($order->order_status=='3'){

                   return __("messages.pending");

                 }else if($order->order_status=='1'){

                    return __("messages.processing");

                 }else if($order->order_status=='7'){

                    return __("messages.refunded");
                 }
                 else if($order->order_status=='4'){

                    return __("messages.out_of_delivery");
                 }
                 
            })
            ->editColumn('total', function ($order) {
                 return $order->currency.$order->total;
            })      
            ->make(true);
  }

  public function sendordermail(Request $request){
       $user=User::find($request->get("user_id"));
       $user->pdffile=public_path().'/pdf/'.'/'.$request->get("filename");
        try {
           
               $result=Mail::send('email.view_order', ['user' => $user], function($message) use ($user){
                   $message->to($user->email,$user->first_name)->subject('shop on');
                   $message->attach($user->pdffile);
                });
            
        } catch (\Exception $e) {
        }
        Session::flash('message',__('messages_error_success.mail_send_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
  }

  public function latestreview(){
      $order =Review::with('product','userdata')->take(10)->get();
         return DataTables::of($order)
            ->editColumn('product_id', function ($order) {
                if($order->product){
                         return $order->product->name;
                }
            })
            ->editColumn('customer', function ($order) {
               if($order->userdata){
                 return $order->userdata->first_name;
               }
               else{
                  return "";
               }
                
              
            })
            ->editColumn('ratting', function ($order) {
                  return $order->ratting.'/5';
            })
                
            ->make(true);
  }

  public function showtransactionorder(){
     return view("admin.order.transaction");
  }
  
  public function transactiondatatable(){
      $order =Order::orderBy('id','DESC')->where("payment_method",'!=',3)->get();
         return DataTables::of($order)
            ->editColumn('id', function ($order) {
                return $order->id;
            })
            ->editColumn('transaction', function ($order) {
                 if($order->payment_method==2){
                    return $order->charges_id;
                 }
                 if($order->payment_method==1){
                    return $order->paypal_payment_Id;
                 }
                 
            })
            ->editColumn('payment_method', function ($order) {
                  if($order->payment_method==2){
                    return __('messages.paypal');;
                 }
                 if($order->payment_method==1){
                    return __('messages.stripe');;
                 }
            })
                
            ->make(true);
  }

  function getName() { 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
  
    for ($i = 0; $i <5; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  
    return "shopno"."_".date('d-m-Y')."_".$randomString.".pdf"; 
} 
   public function changeorderstatus($order_id,$staus_id){
            DB::beginTransaction();
              try {
                             $msg="";
                             $order=Order::find($order_id);
                             $setting=Setting::find(1);
                             $user=User::find($order->user_id);
                             if(!$user){
                                 Session::flash('message',__('messages_error_success.user_not_exist')); 
                                 Session::flash('alert-class', 'alert-success');
                                 return redirect()->back();
                             }
                             if($staus_id==1){//processing
                                  $order->processing_datetime=$this->getsitedate();
                                  $msg=__('messages_error_success.order_process_msg');
                                  $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                  $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }else if($staus_id==2){//on_hold
                                $order->onhold_datetime=$this->getsitedate();
                                $msg=__('messages_error_success.order_hold_msg');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }else if($staus_id==3){//pending
                                $order->pending_datetime=$this->getsitedate();
                                $msg=__('messages_error_success.order_pending_msg');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }
                             else if($staus_id==5){//completed
                                $order->completed_datetime=$this->getsitedate();
                                $msg=__('messages_error_success.order_complete_msg');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }
                             else if($staus_id==6){//cancel
                                $order->cancel_datetime=$this->getsitedate();
                                $msg=__('messages_error_success.order_cancel_msg');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }
                             else if($staus_id==7){//refund
                                $order->refund_datetime=$this->getsitedate();
                                $msg=__('messages_error_success.order_refund_msg');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }
                            
                               else if($staus_id==4){//out of delivery
                                $order->outfordelivery_datetime=$this->getsitedate();
                                $msg=__('messages_error_success.order_out_of_delivery_msg');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                                
                             }

                             $order->order_status=$staus_id;
                             $order->save();
                             if($user){
                                 $user->order_msg=$msg;
                                 $user->order_id=$order->id;
                                   try {
                                        if(Config::get('mail.username')!=""&&$setting->customer_order_status=='1'){
                                          Mail::send('email.customer_order_status', ['user' => $user], function($message) use ($user){
                                                   $message->to($user->email,$user->first_name)->subject('shop on');
                                                 });
                                        }
                                    } catch (\Exception $e) {
                                    }
                             }
                             DB::commit();
                             Session::flash('message',__('messages_error_success.order_status_change')); 
                             Session::flash('alert-class', 'alert-success');
                             return redirect()->back();
                    }
              catch (\Exception $e) {
                   DB::rollback();
                   Session::flash('message',$e); 
                   Session::flash('alert-class', 'alert-danger');
                   return redirect()->back();       
              }
   }


     public function send_notification_android($key,$user_id,$msg,$id){
          $getuser=Token::where("type",1)->where("user_id",$user_id)->get();
          if(count($getuser)!=0){               
               $reg_id = array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
               $registrationIds =  $reg_id;    
               $message = array(
                    'message' => $msg,
                    'key'=>'order',
                    'title' => __('messages.order_status'),
                    'order_id'=>$id
                );
               $fields = array(
                  'registration_ids'  => $registrationIds,
                  'data'              => $message
               );

               $url = 'https://fcm.googleapis.com/fcm/send';
               $headers = array(
                 'Authorization: key='.$key,// . $api_key,
                 'Content-Type: application/json'
               );
              $json =  json_encode($fields);   
              try {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
                    $result = curl_exec($ch);  

                    if ($result === FALSE){
                       die('Curl failed: ' . curl_error($ch));
                    }     
                   curl_close($ch);
                   $response=json_decode($result,true); 
                  } catch (\Exception $e) {
                    return 0;
                 }
             if(isset($response)&&$response['success']>0)
              {
                   return 1;
              }
            else
               {
                  return 0;
               }
        }
        return 0;
   }
   public function send_notification_IOS($key,$user_id,$msg,$id){
      $getuser=Token::where("type",2)->where("user_id",$user_id)->get();
         if(count($getuser)!=0){               
               $reg_id = array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
                $registrationIds =  $reg_id;    
                $message = array(
                   'body'  => $msg,
                   'title'     => __('messages.notification'),
                   'vibrate'   => 1,
                   'sound'     => 1,
                   'key'=>'order',
                   'order_id'=>$id
               );
               $fields = array(
                  'registration_ids'  => $registrationIds,
                  'data'              => $message
               );

               $url = 'https://fcm.googleapis.com/fcm/send';
               $headers = array(
                 'Authorization: key='.$key,// . $api_key,
                 'Content-Type: application/json'
               );
              $json =  json_encode($fields);   
               try {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
                    $result = curl_exec($ch);  

                    if ($result === FALSE){
                       die('Curl failed: ' . curl_error($ch));
                    }     
                   curl_close($ch);
                   $response=json_decode($result,true); 
                  } catch (\Exception $e) {
                    return 0;
                 }
             if(isset($response)&&$response['success']>0)
              {
                   return 1;
              }
            else
               {
                  return 0;
               }
        }
        return 0;
   }

   public function getsitedate(){
            $setting=Setting::find(1);
            $date_zone=array();
            $timezone=$this->generate_timezone_list();
                foreach($timezone as $key=>$value){
                      if($setting->default_timezone==$key){
                              $date_zone=$value;
                      }
                }
            date_default_timezone_set($date_zone);   
            return date('d-m-Y h:i:s');                    
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
  
     public function cashorder(Request $request){
        $setting=Setting::find(1);
        $shipping=Shipping::all();
        $cartCollection = Cart::getContent();
        $gettimezone=$this->gettimezonename($setting->default_timezone);
        date_default_timezone_set($gettimezone);
        $input = $request->input();
        DB::beginTransaction();
        try {
                $store=new Order();
                        $store->user_id=Auth::id();
                        $store->orderdate=date("d-m-Y h:i:s");
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
                        $getjson=$this->getorderjson();
                        if($request->get("couponcode")){
                            $datacoupoun=$this->verifiedcoupon($request->get("couponcode"));
                            if($datacoupoun->discount_type==1){
                                $coupon_price=(Cart::gettotal()*$datacoupoun->value)/100;
                            }else{
                                $coupon_price=$datacoupoun->value;
                            }
                            $store->is_freeshipping=$datacoupoun->free_shipping;
                            $charges=0;
                            if($datacoupoun->free_shipping==1){
                                $store->shipping_charge="0.00";
                            }else{
                                    if($request->get("shipping_type")==1){
                                        $charges=$shipping[0]->cost;
                                        $store->shipping_charge=$shipping[0]->cost;
                                    }else{
                                        $charges=$shipping[1]->cost;
                                        $store->shipping_charge=$shipping[1]->cost;
                                    }
                            }
                            
                            $store->coupon_code=$request->get("couponcode");
                            $store->coupon_price=$coupon_price;
                        }else{
                            $store->is_freeshipping=0;
                            if($request->get("shipping_type")==1){
                                $charges=$shipping[0]->cost;
                                $store->shipping_charge=$shipping[0]->cost;
                            }else{
                                $charges=$shipping[1]->cost;
                                $store->shipping_charge=$shipping[1]->cost;
                            }
                            $store->coupon_code="";
                            $store->coupon_price="";
                            $coupon_price=0;
                        }
                        $store->taxes_charge=number_format($getjson["total"], 2, '.', '');;
                        $total=Cart::gettotal()+$getjson["total"]+$charges-$coupon_price;
                        $store->total=number_format($total, 2, '.', '');
                       
                        $store->order_status='3';
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
                   if($request->get("payment_method")==2){
                       try{
                        \Stripe\Stripe::setApiKey(Session::get("stripe_secert"));
                          $unique_id = uniqid(); 
                          $charge = \Stripe\Charge::create(array(
                              'description' => "Amount: ".number_format($total, 2, '.', '').' - '. $unique_id,
                              'source' => $input['stripeToken'],                    
                              'amount' => (int)(number_format($total, 2, '.', '') * 100), 
                              'currency' => 'USD'
                          ));
                          $data=Order::find($store->id);
                          $data->charges_id=$charge->id;
                          $data->save();
                        }catch (\Exception $e) {
                           Session::flash('message', __('messages_error_success.payment_fail')); 
                            Session::flash('alert-class', 'alert-success');
                            return Redirect::route('checkout');
                      }
                   }
                      $data=array();
                      $data['email']=$setting->email;
                      $data['name']="Shop";
                      $data['customer_name']=$request->get("order_firstname")." ".$request->get("order_lastname");
                      $data['order_amount']=number_format($total, 2, '.', '');
                      try {
                            if(Config::get('mail.username')!=""&&$$setting->admin_order_mail=='1'){
                                     Mail::send('email.orderdetail', ['user' => $data], function($message) use ($data){
                                         $message->to($data['email'],$data['name'])->subject('shop on');
                                     });
                            }
                       } catch (\Exception $e) {

                       }
                      
                DB::commit();
                  Cart::clear();
                    Session::flash('message',__('messages_error_success.order_place_success')); 
                    Session::flash('alert-class', 'alert-success');
                     return redirect("vieworder/".$store->id);
                } catch (\Exception $e) {
                     DB::rollback();
                    Session::flash('message',$e); 
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->back();
          }
     }
    
     function headreadMoreHelper($story_desc, $chars =35) {
    $story_desc = substr($story_desc,0,$chars);  
    $story_desc = substr($story_desc,0,strrpos($story_desc,' '));  
    $story_desc = $story_desc;  
    return $story_desc;  
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

   public function notification($act){
      $data=array();
      if($act==1){
         $result=$this->haveOrdersNotification();
           $orderdata=$this->haveOrdersdata();
            if(isset($result)){
               $data = array(
                      "status" => http_response_code(),
                      "request" => "success",
                      "response" => array(
                      "message" => "Request Completed Successfully",
                      "total" => $result,
                      "orderdata"=>$orderdata
               )
             );
           }
           $updatenotify=$this->updatenotify();

      }
      else{
           $result=$this->haveOrdersNotification();
           $orderdata=$this->haveOrdersdata();
            if(isset($result)){
               $data = array(
                      "status" => http_response_code(),
                      "request" => "success",
                      "response" => array(
                      "message" => "Request Completed Successfully",
                      "total" => $result,
                      "orderdata"=>$orderdata
               )
             );
           }
       }
       return $data;
     }

     public function haveOrdersNotification(){
        $order=Order::where("notify",'1')->get();
        return count($order);
     }
      public function haveOrdersdata(){
        $order=Order::where("notify",'1')->get();
        return count($order);
     }

     public function updatenotify(){
      $order=Order::where("notify",'1')->get();
      foreach ($order as $k) {
         $k->notify='0';
         $k->save();
      }
      return "done";
     }
}