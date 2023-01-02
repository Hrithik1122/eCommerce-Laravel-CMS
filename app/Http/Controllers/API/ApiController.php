<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App\User;
use App\Model\Categories;
use App\Model\CartData;
use App\Model\Brand;
use App\Model\Offer;
use App\Model\Product;
use App\Model\Seasonaloffer;
use App\Model\Banner;
use App\Model\Deal;
use App\Model\Sepicalcategories;
use App\Model\ContactUs;
use App\Model\Setting;
use App\Model\AttributeSet;
use App\Model\Options;
use App\Model\Optionvalues;
use App\Model\Attributes;
use App\Model\Attributevalues;
use App\Model\Review;
use App\Model\ProductAttributes;
use App\Model\ProductOption;
use App\Model\OrderData;
use App\Model\Taxes;
use App\Model\Order;
use App\Model\Coupon;
use App\Model\FeatureProduct;
use App\Model\Wishlist;
use App\Model\OrderResponse;
use App\Model\PaymentMethod;
use App\Model\ResetPassword;
use App\Model\QueryAns;
use App\Model\QueryTopic;
use App\Model\Token;
use App\Model\Complain;
use App\Model\Pages;
use DateTimeZone;
use DateTime;
use Image;
use Mail;
use DB;
class ApiController extends Controller {
    public function __construct() {
         parent::callschedule();
    }
    public function postplaceorder(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'shipping_method' => 'required',
                      'payment_method' => 'required',
                      'order_name' => 'required',
                      'order_billing_address'=>'required',
                      'order_billing_city'=>'required',
                      'order_billing_pincode'=>'required',
                      'order_phone'=>'required',
                      'order_email'=>'required',
                      'shipping_charges'=>'required',
                      'freeshipping'=>'required',
                      'total_taxes'=>'required',
                      'total_order_price'=>'required',
                      'orderjson'=>'required',
                      'to_ship'=>'required',
                      'subtotal'=>'required'
                    ];
                    if($request->input('to_ship')==1){
                           $rules['order_shipping_city'] = 'required';
                           $rules['order_shipping_pincode'] = 'required';
                           $rules['order_ship_name'] = 'required';
                           $rules['order_shipping_address'] = 'required';
                    }
                    if($request->input('couponcode')!=""){
                           $rules['couponval'] = 'required';
                    }
                    if($request->input('payment_method')=="2"){
                           $rules['stripeToken'] = 'required';
                    }
                    if($request->input('payment_method')=="1"){
                           $rules['pay_pal_paymentId'] = 'required';
                    }
            $messages = array(
                  'user_id.required' => "user_id is required",
                  'shipping_method.required' => "shipping_method is required",
                  'payment_method.required' => "payment_method is required",
                  'to_ship.required' => "to_ship is required",
                  'order_name.required' => "Order Name is required",
                  'order_billing_address.required' => "order_billing_address is required",
                  'order_billing_city.required' => "order_billing_city is required",
                  'order_billing_pincode.required' => "order_billing_pincode is required",
                  'order_phone.required' => "order_phone is required",
                  'order_email.required' => "order_email is required",
                  'shipping_type.required' => "shipping_type is required",
                  'freeshipping.required'=>'freeshipping is required',
                  'shipping_charges.required'=>'shipping_charges is required',
                  'total_taxes.required'=>'total_taxes is required',
                  'total_order_price.required'=>'total_order_price is required',
                  'orderjson.required'=>'orderjson is required',
                  'order_shipping_city.required'=>'order_shipping_city is required',
                  'order_shipping_pincode.required'=>'order_shipping_pincode is required',
                  'order_ship_name.required'=>'order_ship_name is required',
                  'order_shipping_address.required'=>'order_shipping_address is required',
                  'couponval.required'=>'couponval is required',
                  'stripeToken.required'=>'stripeToken is required',
                  'pay_pal_paymentId.required'=>'pay_pal_paymentId is required',
                  'subtotal.required'=>'subtotal is required'
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['msg'] = $message;
            } else {
                    $setting=Setting::find(1);
                    DB::beginTransaction();
                    try {
                            $store=new Order();
                            $store->user_id=$request->get("user_id");
                            $store->orderdate=$this->getsitedate();
                            $store->shipping_method=$request->get("shipping_method");
                            $store->payment_method=$request->get("payment_method");
                            $store->billing_first_name=$request->get("order_name");
                            $store->billing_address=$request->get("order_billing_address");
                            $store->billing_city=$request->get("order_billing_city");
                            $store->billing_pincode=$request->get("order_billing_pincode");
                            $store->phone=$request->get("order_phone");
                            $store->email=$request->get("order_email");
                            $store->to_ship=$request->get("to_ship");
                            $store->notes=$request->get("order_notes");
                            if($request->input('to_ship')==1){
                                $store->shipping_city=$request->get("order_shipping_city");
                                $store->shipping_pincode=$request->get("order_shipping_pincode");
                                $store->shipping_first_name=$request->get("order_ship_name");
                                $store->shipping_address=$request->get("order_shipping_address");
                            }
                          
                            $store->subtotal=$request->get("subtotal");
                           // $store->shipping_method=$request->get("shipping_type");
                            $store->shipping_charge=$request->get("shipping_charges");
                            $store->is_freeshipping=$request->get("freeshipping");
                            $store->taxes_charge=$request->get("total_taxes");
                            $store->total=$request->get("total_order_price");
                            $store->coupon_code=$request->get("couponcode");
                            $store->coupon_price=$request->get("couponval");
                            $store->order_status='3';
                            $store->save();
                            $storeres=new OrderResponse();
                            $storeres->order_id=$store->id;
                            $storeres->desc=$request->get("orderjson");
                            $storeres->save();
                            $product_ids=array();
                               $jsondata=json_decode($request->get("orderjson"));
                     
                              foreach($jsondata->order as $k) {
                                  $add=new OrderData();
                                  $product_ids[]=$k->ProductId;
                                  $add->order_id=$store->id;
                                  $add->product_id=$k->ProductId;
                                  $add->quantity=$k->ProductQty;
                                  $add->price=$k->ProductAmt;
                                  $add->total_amount=$k->ProductTotal;
                                  $add->tax_charges=$k->tax_amount;
                                  $add->tax_name=$k->tax_name;
                                  $add->option_name=$k->exterdata->option;
                                  $add->label=$k->exterdata->label;
                                  $add->option_price=$k->exterdata->price;
                                  $add->save();
                              }
                              if($request->get("payment_type")=="Stripe"){
                               
                                    \Stripe\Stripe::setApiKey($setting->stripe_secret);
                                    $unique_id = uniqid(); 
                                    $charge = \Stripe\Charge::create(array(
                                       'description' => "Amount: ".$request->get("total_order_price").' - '. $unique_id,
                                       'source' => $request->get("stripeToken"),                    
                                       'amount' => (int)($request->get("total_order_price") * 100), 
                                       'currency' => 'USD'
                                    ));
                                    $data=Order::find($store->id);
                                    $data->charges_id=$charge->id;
                                    $data->save();
                               
                             }
                              if($request->get("payment_type")=="Paypal"){
                                $data=Order::find($store->id);
                                $data->pay_pal_paymentId=$request->get("pay_pal_paymentId");
                                $data->save();
                             }
                             
                                  $data=array();
                                  $data['email']=$setting->email;
                                  $data['name']="Shop";
                                  $data['customer_name']=$request->get("order_firstname")." ".$request->get("order_lastname");
                                  $data['order_amount']=$request->get("total_order_price");
                                   try {
                                         if($setting->admin_order_mail=='1'){
                                            Mail::send('email.orderdetail', ['user' => $data], function($message) use ($data){
                                                $message->to($data['email'],$data['name'])->subject('shop on');
                                            });
                                         }
                                    } catch (\Exception $e) {
                                    }
                                 
                             DB::commit();
                                foreach ($product_ids as $k) {
                                  $data=CartData::where("product_id",$k)->where("user_id",$request->get("user_id"))->first();
                                  if(isset($data)){
                                       $data->delete();
                                  }
                                 
                                }
                                  $response = array("status" => 1, "msg" => "Order Placed Successfully","data" =>$store->id); 
                             } catch (\Exception $e) {
                                 DB::rollback();
                                $response = array("status" => 0, "msg" => "Something wrong","data"=>$e); 
                       }
            }
            return Response::json(array("data"=>$response));
   }
   
   public function order_cancle_by_user(Request $request){
           $response = array("status" => "0", "register" => "Validation error");
           $rules = [
                      'order_id' => 'required'               
                    ];                    
            $messages = array(
                      'order_id.required' => "order_id is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['msg'] = $message;
            } else {
                        $order=Order::find($request->get('order_id'));
                        $setting=Setting::find(1);
                        
                        if($order){
                            $user=User::find($order->user_id);
                                $order->cancel_datetime=$this->getsitedate();
                                $order->order_status = '6';
                                $msg=__('messages_error_success.order_cancel_msg');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                                $order->save();
                                $response = array("status" =>1, "msg" => "Order Cancel Successfully");
                        }else{
                            $response = array("status" =>0, "msg" => "Order Not Found");
                        }
                         
           }
           return Response::json(array("data"=>$response));
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

 
  
     public function postreview(Request $request){
          $response = array("status" => "0", "register" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'product_id' => 'required',
                      'review' => 'required',
                      'ratting' => 'required'                 
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'product_id.required' => "product_id is required",
                      'review.required' => "review is required",
                      'ratting.required' => "ratting is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['msg'] = $message;
            } else {
                $data=array();
                $data=new Review();
                $data->product_id=$request->get("product_id");
                $data->user_id=$request->get("user_id");
                $data->review=$request->get("review");
                $data->ratting=$request->get("ratting");
                $data->save();
                $response = array("status" =>1, "msg" => "Review Add Successfully","data"=>$data);
           }
           return Response::json(array("data"=>$response));
     }

    public function userregister(Request $request){
          $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'name' => 'required',
                      'email' => 'required|unique:users',
                      'password' => 'required',
                      'phone'=>'required',
                      "token"=>"required"              
                    ];                    
            $messages = array(
                      'name.required' => "name is required",
                      'email.unique' => 'Email Already exist',
                      'email.required' => "email are required",                      
                      'password.required' => "password is required",
                      'phone.required'=>"phone is required",
                      'token.required'=>"Token is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                $setting=Setting::find(1);  
                $user =User::where("email",$request->get("email"))->get(); 
                 if(count($user)==0){
                           $user=new User();
                            $user->first_name=$request->get("name");
                            $user->email=$request->get("email");
                            $user->password=$request->get("password");
                            $user->is_email_verified='1';
                            $user->login_type=1;
                            $user->phone=$request->get("phone");
                            $user->user_type="1";                 
                            $user->save();
                            $gettoken=Token::where("token",$request->get("token"))->update(["user_id"=>$user->id]);
                          
                          $response = array("status" =>1, "msg" => "Register Successfully","data"=>$user);
                 
               }
                 else{
                  $response = array("status" =>0, "msg" => "Something wrong");
                 }                
           }
           return $response;
    }

    public function editprofile(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'name' => 'required',
                      'address' => 'required',
                      'phone' => 'required',
                      'user_id'=>"required"                
                    ];                    
            $messages = array(
                      'name.required' => "name is required",
                      'address.required' => 'address is required',
                      'phone.required' => "phone is required",                      
                      'user_id.required' => "user_id is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                 $setting=Setting::find(1);  
                 $user =User::find($request->get("user_id")); 
                 if($user){
                    $user->first_name=$request->get("name");
                    $user->address=$request->get("address");
                    $user->phone=$request->get("phone");
                    $user->save();
                    $response = array("status" =>1, "msg" => "Profile Update Successfully","data"=>$user);
                 }
                 else{
                  $response = array("status" =>0, "msg" => "User not Found");
                 }                
           }
           return $response;
    }

     public function forgotpassword(Request $request){
            $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'email' => 'required'           
                    ];                    
            $messages = array(
                      'email.required' => "email is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                 $setting=Setting::find(1);  
                 $checkmobile=User::where("email",$request->get("email"))->get();
                  if(count($checkmobile)!=0){
                      $code=mt_rand(100000, 999999);
                      $store=array();
                      $store['email']=$checkmobile[0]->email;
                      $store['name']=$checkmobile[0]->name;
                      $store['code']=$code;
                      $add=new ResetPassword();
                      $add->user_id=$checkmobile[0]->id;
                      $add->code=$code;
                      $add->save();
                       try {
                              Mail::send('email.forgotpassword', ['user' => $store], function($message) use ($store){
                                $message->to($store['email'],$store['name'])->subject('Shop');
                            });
                       } catch (\Exception $e) {
                       }
                      $response = array("status" =>1, "msg" => "Mail Send Successfully ");
                  }
                 else{
                    $response = array("status" =>0, "msg" => "Email Id Not Existe");
                 }                
           }
           return $response;  
    }
    function getcode() { 
          $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
          $randomString = ''; 
        
          for ($i = 0; $i <10; $i++) { 
              $index = rand(0, strlen($characters) - 1); 
              $randomString .= $characters[$index]; 
          } 
        
          return $randomString; 
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
    public function getcurrency(){
            $setting=Setting::find(1);
            $cur=explode("-",$setting->default_currency);  
            return $cur[1];                  
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


   public function categoryoffer(){
      $date=date('Y-m-d');
      $getcategory=Categories::where("parent_category",0)->where("is_active",'1')->where("is_delete","0")->get();
      foreach ($getcategory as $k) {
          $od=array();
          $offers=Offer::where("category_id",$k->id)->get();
          foreach ($offers as $of) {
               $start_date=date("Y-m-d",strtotime($of->start_date)); 
               $end_date=date("Y-m-d",strtotime($of->end_date));
            if(($date>=$start_date)&&($date<=$end_date)){
                  $od[]=$of;
            }
          }
          $k->offers=$od;
          $getsubcategory=Categories::where("parent_category",$k->id)->where("is_delete","0")->where("is_active",'1')->get();
          $k->subcategory=$getsubcategory;
      }
      $response = array(
        'status' =>1,
        "data"=>$getcategory
      );
      return Response::json($response);
   }

 public function bestselling($user_id){
       $data=DB::table('order_data')
                 ->select('product_id', DB::raw('count(*) as total'))
                 ->orderby('total','DESC')
                 ->join('products', 'products.id', '=', 'order_data.product_id')
                 ->where('products.is_deleted','0')
                 ->where('products.status','1')
                 ->groupBy('product_id') 
                 ->paginate(10);
                 if(count($data)!=0){
                      foreach ($data as $k) {
                        $dat=[];
                        $getproduct=Product::where('status','1')->where("id",$k->product_id)->first();
                        if($getproduct){
                           $avgStar = Review::where("product_id",$k->product_id)->avg('ratting');
                        $wish=Wishlist::where("product_id",$k->product_id)->where("user_id",$user_id)->get();
                        $total=Review::where("product_id",$k->product_id)->get();
                        
                        $dat['id']=$getproduct->id;
                        $dat['name']=$getproduct->name;
                        $dat['image']=asset('public/upload/product/').'/'.$getproduct->basic_image;
                        $dat['MRP']=$getproduct->MRP;
                        $dat['ratting']=round($avgStar);
                        $dat['totalreview']=count($total);
                        $dat['price']=$getproduct->selling_price;
                        $dat['wish']=count($wish);
                        $dat['discount']=$getproduct->discount;
                        $k->productdetail=$dat;

                   }           
        }
        $total=Wishlist::where("user_id",$user_id)->get();
        if(isset($user_id)&&$user_id!=0){
            $total1=count($total);
        }else{
            $total1=0;
        }
        
        $cartdata=CartData::where("user_id",$user_id)->get();
        if(isset($user_id)&&$user_id!=0){
            $cart1=count($cartdata);
        }else{
            $cart1=0;
        }
       // echo "<pre>";print_r($cartdata);exit;
       $response = array(
        'status' => 1,
        "product"=>array("data"=>$data,"totalwish"=>$total1,"carttotal"=>$cart1)
      );
                 }
                 else{
  $response = array(
        'status' => 0,
        "product"=>$data
      );
                 }
      
      return Response::json($response);
 }

 public function taxlist(){
   $gettax=Taxes::all();
    $response = array(
        'status' => 0,
        "product"=>$gettax
      );
           
      
      return Response::json($response);
 }

 public function mainoffers(){
      $best=array();
      $date=date("Y-m-d");
      $bestoffer=Offer::where("offer_type","1")->orderby('id',"DESC")->get();
      foreach ($bestoffer as $bo) {
          $start_date=date("Y-m-d",strtotime($bo->start_date)); 
          $end_date=date("Y-m-d",strtotime($bo->end_date));
          if(($date>=$start_date)&&($date<=$end_date)){
                  if($bo->is_product=='1'){
                     
                  }
                  if($bo->is_product=='2'){
                    
                  }
                  $best[]=$bo;
          }
        }
     $data=Deal::with('offer')->get();
     foreach ($data as $k) {
      if(isset($k->offer)&&$k->offer->is_product=='1'){
                    
                     $best[]=$k->offer;
                  }
                  if(isset($k->offer)&&$k->offer->is_product=='2'){
                     
                     $best[]=$k->offer;
                  }
       
     }
     $category = Categories::where("parent_category",'0')->where('is_delete','0')->select('id','name','image')->get();
     $response = array(
        'status' =>1,
        "offers"=>$best,
        "category"=>$category
      );
      return Response::json($response);
 }

 public function viewproduct($id,$user_id){
          $product=Product::where("status",'1')->where("is_deleted",'0')->where("id",$id)->first();
          if(!empty($product)){         
                  $main_array=array();
                  $attributearr=array();
                  $attribute_set=array();
                  $data=array();
                  $product->attributes=ProductAttributes::where("product_id",$id)->get();
                   $wish=Wishlist::where("product_id",$id)->where("user_id",$user_id)->get();
                   $product->wish=count($wish);
                   $totalreview=Review::where("product_id",$id)->get();
                   $product->total_review=count($totalreview);
                 
                  
                  $img=array();
          $img[0]=asset('public/upload/product/').'/'.$product->basic_image;
          if($product->additional_image!=""){
                 
                 $images=explode(",",$product->additional_image);
                 $i=1;
                  foreach ($images as $k) {
                      $img[$i]=asset('public/upload/product/').'/'.$k;
                      $i++;
                  }
                 
                  
          }
        $product->additional_image=implode(",",$img);
                
                  $product->options=ProductOption::where("product_id",$id)->first();
        
                  if($product->options!=""){
                      $fnarr=array();
                      $opname=explode(",",$product->options->name);
                      $optype=explode(",",$product->options->type);
                      $opreq=explode(",",$product->options->is_required);
                      $oplabel=explode("#",$product->options->label);
                      $opprice=explode("#",$product->options->price);
                      
                      for($i=0;$i<count($opname);$i++){
                          $dt=array();
                          $dt['optionname']=$opname[$i];
                          $dt['type']=$optype[$i];
                          $dt['required']=$opreq[$i];
                          $name=explode(",",$oplabel[$i]);
                          $price=explode(",",$opprice[$i]);
                          $labelarr=array();
                          for($j=0;$j<count($name);$j++) {
                              if($name[$j]!=""){
                                $labelarr[$j]["label"]=$name[$j];
                                $labelarr[$j]["price"]=$price[$j];
                              }
                              
                          }
                          $dt["optionvalues"]=$labelarr;
                          $fnarr[]=$dt;
                      }
                      $product->options=$fnarr;
                  }
                 
                  $product->review=Review::with('userdata')->where("product_id",$id)->where("is_deleted",'0')->orderby("id","DESC")->take(5)->get();
                   foreach ($product->review as $re) {
                    $re->image=asset("public/upload/profile/").$re->userdata['profile_pic'];
        
                       // $re->userdata['profile_pic']=asset("public/upload/profile/").$re->userdata['profile_pic'];
                   }
                  
                  $avgStar = Review::where("product_id",$id)->avg('ratting');
                  if(empty($avgStar)){
                     $avgStar=0;
                  }
                  else{
                     $avgStar=round($avgStar);
                  }
                  $product->basic_image=asset('public/upload/product/').'/'.$product->basic_image;
                  $product->avgStar=$avgStar;
                  $cat=Categories::find($product->category);
                  $sub=Categories::find($product->subcategory);
                  $br=Brand::find($product->brand);
                  $product->category=$cat->name;
                  $product->subcategory=$sub->name;
                  $product->brand=$br->brand_name;
                  $product->price=$product->selling_price;
                     $response = array(
                        'status' =>1,
                        "offers"=>$product
                      );
          }
          else{
              $response = array(
                'status' =>0,
                "offers"=>"No Product Found"
              );
          }
       return Response::json($response);
 }


  public function addwish(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'product_id' => 'required',
                      'user_id'=>'required'        
                    ];                    
            $messages = array(
                    'product_id.required' => "product_id is required",
                    'user_id.required' => "user_id is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }else {
                $getwish=Wishlist::where("product_id",$request->get("product_id"))->where("user_id",$request->get("user_id"))->first();
                if(!empty($getwish)){
                      $getwish->delete();
                      $total=Wishlist::where("user_id",$request->get("user_id"))->get();
                      $response = array(
                            'status' =>1,
                            "remove"=>"yes",
                            "wish"=>count($total)
                          );
                }
                else{
                   $data=new Wishlist();
                  $data->product_id=$request->get("product_id");
                  $data->user_id=$request->get("user_id");
                  $data->save();
                  $total=Wishlist::where("user_id",$request->get("user_id"))->get();
                  $response = array(
                    'status' =>1,
                    "remove"=>"no",
                    "wish"=>count($total)
                  );
                }
                 
            }
      
      return Response::json($response);
   }



  
  public function Showlogin(Request $request){
        $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'login_type' => 'required',
                      'token' => 'required',
                      'token_type'=>'required',
                      'email' => 'required'      
                    ];
                    if($request->input('login_type')=='1'){
                        $rules['password'] = 'required';
                    }
                    if($request->input('login_type')=='2'||$request->input('login_type')=='3'){
                        $rules['soical_id'] = 'required';
                        $rules['name']='required';
                    }
                   
            $messages = array(
                      'login_type.required' => "login_type is required",
                      'token.required' => "token is required",
                      'token_type.required' => "token_type is required",
                      'email.required' => "email is required",
                      'password.required'=>"password is required",
                      "soical_id.required"=>"soical_id is required",
                      "name.required"=>"name is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['register'] = $message;
            } else {
                      $setting=Setting::find(1);
                      if($request->input('login_type')=='1'){
                      $user=User::where("email",$request->get("email"))->where("password",$request->get("password"))->first();
                      if($user){
                              if($setting->customer_reg_email=='1'&&$user->is_email_verified=='0'){
                                   $response = array("status" =>0, "msg" => "Please Verified Your Email");
                              }
                              else{
                              $gettoken=Token::where("token",$request->get("token"))->first();
                              if(!$gettoken){
                                     $store=new Token();
                                     $store->token=$request->get("token");
                                     $store->type=$request->get("token_type");
                                     $store->user_id=$user->id;
                                     $store->save();
                              }
                              else{
                                     $gettoken->user_id=$user->id;
                                     $gettoken->save();
                              }
                                    
                                     if($user->soical_id==null){
                                        $user->soical_id="";
                                     }
                                   if($user->billing_address==null){
                                      $user->billing_address="";
                                   }
                                   if($user->shipping_address==null){
                                      $user->shipping_address="";
                                   }
                                   if($user->profile_pic==null){
                                      $user->profile_pic="";
                                   }
                                   if($user->first_name==null){
                                      $user->first_name="";
                                   }
                                   if($user->address==null){
                                      $user->address="";
                                   }
                                   if($user->phone==null){
                                      $user->phone="";
                                   }
                                   if($user->last_name==null){
                                      $user->last_name="";
                                   }
                                    $cartdata=CartData::where("user_id",$user->id)->get();
                                    $wishdata=Wishlist::where("user_id",$user->id)->get();
                                    if(count($wishdata)>0){
                                        $user->totalwish=count($wishdata);
                                    }
                                    if($user->id==0){
                                        $user->totalwish = 0;
                                    }
                                    $user->cart=count($cartdata);
                                    
                                      $response = array("status" =>1, "msg" => "Login Successfully","data"=>$user);
                               }
                              }
                            else{
                                 $response = array("status" =>0, "msg" => "Login Credentials Are Wrong");
                            }
                 }
                 if($request->input('login_type')=='2'){
                    $checkuser=User::where("email",$request->get("email"))->orwhere("soical_id",$request->get("soical_id"))->first();
                    if($checkuser){//login
                      $gettoken=Token::where("token",$request->get("token"))->first();
                              if(!$gettoken){
                                     $store=new Token();
                                    $store->token=$request->get("token");
                             $store->type=$request->get("token_type");
                             $store->user_id=$checkuser->id;
                             $store->save();
                              }
                               else{
                                     $gettoken->user_id=$checkuser->id;
                                     $gettoken->save();
                              }
                          
                          if($checkuser->soical_id==null){
                                      $checkuser->soical_id="";
                                   }
                                   if($checkuser->billing_address==null){
                                      $checkuser->billing_address="";
                                   }
                                   if($checkuser->shipping_address==null){
                                      $checkuser->shipping_address="";
                                   }
                                   if($checkuser->profile_pic==null){
                                      $checkuser->profile_pic="";
                                   }
                                   if($checkuser->first_name==null){
                                      $checkuser->first_name="";
                                   }
                                   if($checkuser->address==null){
                                      $checkuser->address="";
                                   }
                                   if($checkuser->phone==null){
                                      $checkuser->phone="";
                                   }
                                   if($checkuser->last_name==null){
                                      $checkuser->last_name="";
                                   }
                                    if($checkuser->permissions==null){
                                      $checkuser->permissions="";
                                   }
                                    if($checkuser->last_login==null){
                                      $checkuser->last_login="";
                                   }
                                  
                                     if($request->get("image")!=""){
                                         $png_url = "profile-".mt_rand(100000, 999999).".png";
                                         $path = public_path().'/upload/profile/' . $png_url;
                                         $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                                          $checkuser->profile_pic=$png_url;
                                     }
                           
                                    $checkuser->soical_id=$request->get("soical_id");
                                    $checkuser->login_type=$request->input('login_type');
                                    $checkuser->save();
                                    $cartdata=CartData::where("user_id",$checkuser->id)->get();
                                    $wishdata=Wishlist::where("user_id",$checkuser->id)->get();
                                    $checkuser->cart=count($cartdata);
                                   
                                    $checkuser->totalwish=count($wishdata);
                           $response = array("status" =>1, "msg" => "Login Successfully","data"=>$checkuser);
                    }
                    else{//register
                       
                            $png_url="";
                            if($request->get("image")!=""){
                                 $png_url = "profile-".mt_rand(100000, 999999).".png";
                                 $path = public_path().'/upload/profile/' . $png_url;
                                 $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                            }
                            $str=explode(" ", $request->get("name"));
                            $store=new User();
                            $store->first_name=$str[0];
                            $store->email=$request->get("email");
                            $store->login_type=$request->get("login_type");
                            $store->is_email_verified="1";
                            $store->profile_pic=$png_url;
                            $store->soical_id=$request->get("soical_id");
                            $store->save();
                            $gettoken=Token::where("token",$request->get("token"))->update(["user_id"=>$store->id]);
                             if($store->soical_id==null){
                                      $store->soical_id="";
                                   }
                                   if($store->billing_address==null){
                                      $store->billing_address="";
                                   }
                                   if($store->shipping_address==null){
                                      $store->shipping_address="";
                                   }
                                   if($store->profile_pic==null){
                                      $store->profile_pic="";
                                   }
                                   if($store->first_name==null){
                                      $store->first_name="";
                                   }
                                   if($store->address==null){
                                      $store->address="";
                                   }
                                   if($store->phone==null){
                                      $store->phone="";
                                   }
                                   if($store->last_name==null){
                                      $store->last_name="";
                                   }
                                     if($store->permissions==null){
                                      $store->permissions="";
                                   }
                                    if($store->last_login==null){
                                      $store->last_login="";
                                   }

                                    $cartdata=CartData::where("user_id",$store->id)->get();
                                    $wishdata=Wishlist::where("user_id",$store->id)->get();
                                    $store->cart=count($cartdata);
                                    $store->totalwish=count($wishdata);
                             $response = array("status" =>1, "msg" => "Login Successfully","data"=>$store);
                      
                        
                    }
                 }
                if($request->input('login_type')=='3'){
                       $checkuser=User::where("email",$request->get("email"))->orwhere("soical_id",$request->get("soical_id"))->first();
                    if($checkuser){//login
                      
                          $gettoken=Token::where("token",$request->get("token"))->first();
                              if(!$gettoken){
                                     $store=new Token();
                           $store->token=$request->get("token");
                           $store->type=$request->get("token_type");
                           $store->user_id=$checkuser->id;
                           $store->save();
                              } else{
                                     $gettoken->user_id=$checkuser->id;
                                     $gettoken->save();
                              }
                            if($checkuser->soical_id==null){
                                      $checkuser->soical_id="";
                                   }
                           
                                   if($checkuser->billing_address==null){
                                      $checkuser->billing_address="";
                                   }
                                   if($checkuser->shipping_address==null){
                                      $checkuser->shipping_address="";
                                   }
                                   if($checkuser->profile_pic==null){
                                      $checkuser->profile_pic="";
                                   }
                                   if($checkuser->first_name==null){
                                      $checkuser->first_name="";
                                   }
                                   if($checkuser->address==null){
                                      $checkuser->address="";
                                   }
                                   if($checkuser->phone==null){
                                      $checkuser->phone="";
                                   }
                                   if($checkuser->last_name==null){
                                      $checkuser->last_name="";
                                   }
                                    if($checkuser->permissions==null){
                                      $checkuser->permissions="";
                                   }
                                    if($checkuser->last_login==null){
                                      $checkuser->last_login="";
                                   }
                                   if($request->get("image")!=""){
                                            $png_url = "profile-".mt_rand(100000, 999999).".png";
                                            $path = public_path().'/upload/profile/' . $png_url;
                                            $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                                            $checkuser->profile_pic=$png_url;
                                     }
                           
                                    $checkuser->soical_id=$request->get("soical_id");
                                    $checkuser->login_type=$request->input('login_type');
                                    $checkuser->save();
                                    $cartdata=CartData::where("user_id",$checkuser->id)->get();
                                    $wishdata=Wishlist::where("user_id",$checkuser->id)->get();
                                    $checkuser->cart=count($cartdata);
                                    $checkuser->totalwish=count($wishdata);
                           $response = array("status" =>1, "msg" => "Login Successfully","data"=>$checkuser);
                    }
                    else{//register
                       
                            $png_url="";
                            if($request->get("image")!=""){
                                 $png_url = "profile-".mt_rand(100000, 999999).".png";
                                 $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                            }
                            $str=explode(" ", $request->get("name"));
                            $store=new User();
                            $store->first_name=$str[0];
                            $store->email=$request->get("email");
                            $store->login_type=$request->get("login_type");
                            $store->profile_pic=$png_url;
                            $store->is_email_verified="1";
                            $store->soical_id=$request->get("soical_id");
                            $store->save();
                            $gettoken=Token::where("token",$request->get("token"))->update(["user_id"=>$store->id]);
                            if($store->soical_id==null){
                                      $store->soical_id="";
                                   }
                                   if($store->billing_address==null){
                                      $store->billing_address="";
                                   }
                                   if($store->shipping_address==null){
                                      $store->shipping_address="";
                                   }
                                   if($store->profile_pic==null){
                                      $store->profile_pic="";
                                   }
                                   if($store->first_name==null){
                                      $store->first_name="";
                                   }
                                   if($store->address==null){
                                      $store->address="";
                                   }
                                   if($store->phone==null){
                                      $store->phone="";
                                   }
                                   if($store->last_name==null){
                                      $store->last_name="";
                                   }
                                     if($store->permissions==null){
                                      $store->permissions="";
                                   }
                                    if($store->last_login==null){
                                      $store->last_login="";
                                   }
                                   $cartdata=CartData::where("user_id",$store->id)->get();
                                    $wishdata=Wishlist::where("user_id",$store->id)->get();
                                    $store->cart=count($cartdata);
                                    $store->totalwish=count($wishdata);
                             $response = array("status" =>1, "msg" => "Login Successfully","data"=>$store);
                        }
                        
                   
                 }
            }
            return Response::json(array("data"=>$response));
   }
 public function file_get_contents_curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
   public function getwishlist(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'user_id'=>'required'        
                    ];                    
            $messages = array(
                    'user_id.required' => "user_id is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }else {
               $data=Wishlist::where("user_id",$request->get("user_id"))->get();
               if(count($data)!=0){
                  foreach ($data as $k) {
                     $product=Product::where("id",$k->product_id)->where("status",'1')->where("is_deleted",'0')->select("id","name","basic_image","selling_price")->first();
                     $product->basic_image=asset('public/upload/product/').'/'.$product->basic_image;

                     $k->product=$product;
                    
                  }
                  $response = array(
                    'status' =>1,
                    "Wish"=>$data
                  );
               }   
               else{
                   $response = array(
                    'status' =>0,
                    "Wish"=>"No WishList Found"
                  );
               }
            }
      
      return Response::json($response);
   }
     public function verifiedcoupon1(Request $request){
        $response = array("success" => "0", "discount" => "Not Set");
           $rules = [
                      'coupon_code' => 'required',
                      'user_id' => 'required',
                      'total' => 'required',
                      'product'=>'required'              
                    ];                    
            $messages = array(
                      'coupon_code.required' => "coupon_code is required",
                      'user_id.required' => "user_id is required",
                      'total.required' => "total is required",
                      'product.required'=>"Product is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['discount'] = $message;
            } else {
                 $date=date("Y-m-d");
                 $data=Coupon::where("code",$request->get("coupon_code"))->where("status",'1')->first();
                 if(!$data){
                  $response = array("status" =>0, "discount" => "Coupon Not Found");
                 }
                else{
                      $start_date=date("Y-m-d",strtotime($data->start_date)); 
                      $end_date=date("Y-m-d",strtotime($data->end_date));

                        if(($date>=$start_date)&&($date<=$end_date)){

                        $order=Order::where("coupon_code",$request->get("coupon_code"))->get();
                        $orderuser=Order::where("coupon_code",$request->get("coupon_code"))->where("user_id",$request->get("user_id"))->get();
                              $temp=0;
                              $arr=explode(",",$request->get("product"));
                              if($data->coupon_on=='1'){
                                $codepro=explode(",", $data->categories);

                                foreach ($arr as $k) {
                                  $getcategory=Product::find($k);  
                                                                
                                  if(in_array($getcategory->category,$codepro)){
                                          $temp=1;
                                  }
                                }
                              
                              }
                              else{                                 
                                 $codepro=explode(",", $data->product);
                                 foreach ($arr as $k) {
                                      if(in_array($k,$codepro)){
                                          $temp=1;
                                      }
                                 }
                              }
                              if($temp==0){
                                   $response = array("status" =>0, "discount" => "Coupon Invaild");
                                   return Response::json(array("data"=>$response));
                              }
                         
                        if($data->usage_limit_per_coupon!=""&&($data->usage_limit_per_coupon<count($order))){
                              $response = array("status" =>0, "discount" =>"Coupon Limit Over");
                        }
                        elseif($data->usage_limit_per_customer!=""&&($data->usage_limit_per_customer<=count($orderuser))){
                              $response = array("status" =>0, "discount" => "Your Coupon Limit Over");
                        }
                        elseif($data->minmum_spend!=""&&$data->minmum_spend>$request->get("total")){
                             $response = array("status" =>0, "discount" => "Not Vaild Coupon,total less than minimum amount of coupon");
                        }
                        elseif($data->maximum_spend!=""&&$data->maximum_spend<=$request->get("total")){
                                 $response = array("status" =>0, "discount" => "Not Valid Coupon,total greater than maximum amount of coupon");
                        }
                        else{

                              $temp=0;
                              $arr=explode(",",$request->get("product"));
                              if($data->coupon_on=='1'){
                                $codepro=explode(",", $data->categories);

                                foreach ($arr as $k) {
                                  $getcategory=Product::find($k);  
                                                                
                                  if(in_array($getcategory->category,$codepro)){
                                          $temp=1;
                                  }
                                }
                              
                              }
                              else{                                 
                                 $codepro=explode(",", $data->product);
                                 foreach ($arr as $k) {
                                      if(in_array($k,$codepro)){
                                          $temp=1;
                                      }
                                 }
                              }
                              if($temp==1){
                                  if($data->discount_type=='1'){
                                   $discount=($request->get("total")*$data->value)/100;
                                  }
                                  else{
                                     $discount=$data->value;
                                  }
                                 $data=array("discount_price"=>$discount,"freeshipping"=>$data->free_shipping);
                                     $response = array("status" =>1,"discount"=>$data);
                              }
                              else{
                                $response = array("status" =>0, "discount" => "Coupon Invaild");
                              }
                             
                           }
                        }
                        else{
                          $response = array("status" =>0, "discount" => "Coupon Invaild");
                        }
                      }
           }
           return Response::json(array("data"=>$response));
   }

   public function showoffers($user_id,$page_no){
        $date=date("Y-m-d");
        $best=array();
        $normal=array();
        $sen_offer=Seasonaloffer::where("is_active","1")->first();  
        $bestoffer=Offer::where("offer_type","1")->orderby('id',"DESC")->get();
        foreach ($bestoffer as $bo) {
          $start_date=date("Y-m-d",strtotime($bo->start_date)); 
          $end_date=date("Y-m-d",strtotime($bo->end_date));
          if(($date>=$start_date)&&($date<=$end_date)){
                  if($bo->is_product=='1'){
                     $bo->new_price="";
                     $bo->product_id="";
                  }
                  if($bo->is_product=='2'){
                     $bo->fixed="";
                     $bo->category_id="";
                  }
                  if($bo->is_product=='3'){
                     $bo->fixed="";
                     $bo->category_id="";
                     $bo->new_price="";
                     $bo->product_id="";
                  }

                  $best[]=$bo;
          }
        }
        $normaloffer=Offer::where("offer_type","2")->orderby('id',"DESC")->get();
       
        foreach ($normaloffer as $bo) {
            $start_date=date("Y-m-d",strtotime($bo->start_date)); 
            $end_date=date("Y-m-d",strtotime($bo->end_date));

            if(($date>=$start_date)&&($date<=$end_date)){
             
                  if($bo->is_product=='1'){
                     $bo->new_price="";
                     $bo->product_id="";
                  }
                  if($bo->is_product=='2'){
                     $bo->fixed="";
                     $bo->category_id="";
                  }
                  if($bo->is_product=='3'){
                     $bo->fixed="";
                     $bo->category_id="";
                     $bo->new_price="";
                     $bo->product_id="";
                  }

                    $normal[]=$bo;
            }
        }
       
        $product=Product::where("special_price","!=","")->where("special_price","!=","")->where("status","1")->where("is_deleted","0")->select("id","name","MRP","discount","selling_price","basic_image","special_price_start","special_price_to")->get();
        $main=array();
        foreach ($product as $k) {
          $start_date=date("Y-m-d",strtotime($k->special_price_start)); 
          $end_date=date("Y-m-d",strtotime($k->special_price_to)); 
          if(($date>=$start_date)&&($date<=$end_date)){
              $k->name=$k->name;
              $getreview=Review::where("product_id",$k->id)->get();
              $k->total_review=count($getreview);
              $avgStar = Review::where("product_id",$k->id)->avg('ratting');
              $k->avgStar=round($avgStar);
              $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
              $k->wish=count($wish);
             
              $k->price=$k->selling_price;
              $k->basic_image=asset("public/upload/product")."/".$k->basic_image;
              unset($k->selling_price);
              unset($k->special_price_start);
              unset($k->selling_price);
              unset($k->special_price_to);
              $main[]=$k;
          } 
         } 
          $found_data=array();
          if(count($main) > 0){
             $found_data = array_slice($main,(($page_no-1)*10),10);
              if(count($found_data) > 0){
                $data=array("big_offer"=>$best,"normal_offer"=>$normal,"product"=>$found_data,"sensonal_offer"=>$sen_offer);
              } else {
                $data=array("big_offer"=>$best,"normal_offer"=>$normal,"product"=>$found_data,"sensonal_offer"=>$sen_offer);
              }
          } else {
             $data=array("big_offer"=>$best,"normal_offer"=>$normal,"product"=>$found_data,"sensonal_offer"=>$sen_offer);
          }
          $response = array("status" =>1, "msg" => "Offer Data","offerdata"=>$data);
       
       return Response::json(array("data"=>$response));
   }

   public function searchproduct(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'search' => 'required'                
                    ];                    
            $messages = array(
                      'search.required' => "search is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['msg'] = $message;
            } else {
                $user_id=$request->get("user_id");
                $product=Product::where("name","like","%".$request->get("search")."%")->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("status",'1')->where("is_deleted",'0')->paginate(10);
                 foreach ($product as $k) {
                         $option=ProductOption::where("product_id",$k->id)->first();
                         $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                         if($avgStar==""){
                            $k->ratting="";
                         }
                         else{
                           $k->ratting=$avgStar;
                         }
                         
                         $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
                         $k->wish=count($wish);
                         $re=Review::where("product_id",$k->id)->get();
                         $k->totalreview=count($re);
                         $k->basic_image=asset('public/upload/product/').'/'.$k->basic_image;
                         $k->price=$k->selling_price;
                         
                         unset($k->selling_price);
                }

                $response = array("status" =>1, "msg" => "Search Result","data"=>$product);
           }
           return Response::json(array("data"=>$response));
   }

   public function addcomplain(Request $request){
            $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'user_id' => 'required',
                      'description'=>'required',
                      'complain_type'=>'required'             
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'description.required' => "description is required",
                      'complain_type.required' => "complain_type is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['msg'] = $message;
            } else {
                $getuser=User::find($request->get("user_id"));
                if($getuser){
                   $product=new Complain();
                   $product->email=$getuser->email;
                   $product->user_id=$request->get("user_id");
                   $product->description=$request->get("description");
                   $product->report_error=$request->get("complain_type");
                   $product->save();                 
                   $response = array("status" =>1, "msg" => "Complain Add Successfully","data"=>$product);
                }else{
                  $response = array("status" =>0, "msg" => "User Not Found");
                }
          }
           return Response::json(array("data"=>$response));
   }

   public function save_token(Request $request){

       $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'token' => 'required',
                      'type'=>'required'             
                    ];                    
            $messages = array(
                      'token.required' => "token is required",
                      'type.required' => "type is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['msg'] = $message;
            } else {
                if($request->get("token")!=""&&$request->get("type")!=""&&$request->get("token")!="null"){
                     $store=new Token();
                     $store->token=$request->get("token");
                     $store->type=$request->get("type");
                     $store->save();
                     $response = array("status" =>1, "msg" => "Token Save Successfully","data"=>$store);
                }
                else{

                 $response = array("status" =>0, "msg" => "Fields is Required");
                }
                
          }
           return Response::json(array("data"=>$response));
   }
   
   public function viewpage($id){
       $page=Pages::find($id);
       if($page){
           $response = array("status" =>1, "msg" => "Page Found","page"=>$page);
       }
       else{
           $response = array("status" =>0, "msg" => "Page not found","page"=>array());
       }
       return Response::json(array("data"=>$response));
   }
   
    public function gethelp($id){
        $gettext=QueryTopic::with("Question")->where("page_id",$id)->get(); 
        if($gettext){
            if(count($gettext)>0){
                $response = array("status" =>1, "msg" => "Help get Successfully","help"=>$gettext);
            }else{
                $response = array("status" =>0, "msg" => "Help Not Found","help"=>array());
            }
            
        }else{
             $response = array("status" =>0, "msg" => "Data Not Found","help"=>array());
        }
         return Response::json(array("data"=>$response));
   }

}
?>

