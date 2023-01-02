<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App\User;
use App\Model\Categories;
use App\Model\OrderData;
use App\Model\Order;
use DateTimeZone;
use DateTime;
use Image;
use Mail;
use DB;
class OrderController extends Controller {
    public function __construct() {
         parent::callschedule();
    }
    
    public function vieworder($id){
        $order=Order::with('userdata')->find($id);
        if($order){
                 $orderdata=OrderData::with('productdata')->where("order_id",$id)->get();
                 $order_details=array();
                 $main_prod=array();
                 foreach ($orderdata as $k) {
                       $arr=array();
                       $arr['basic_image']=asset('public/upload/product/').'/'.$k->productdata->basic_image;
                       $arr["name"]=$k->productdata->name;
                       $getcategory=Categories::find($k->productdata->category);
                       $arr["category"]=$getcategory->name;
                       $arr["qty"]=$k->quantity;
                       $arr["price"]=$k->price;
                       $arr["amount"]=$k->total_amount;
                       $main_prod[]=$arr;
                 }
                 $order_details=array(
                      "products"=>$main_prod,
                      "subtotal"=>$order->subtotal,
                      "shipping_charge"=>$order->shipping_charge,
                      "shipping_method"=>$order->shipping_method,
                      "freeshipping"=>$order->is_freeshipping,
                      "taxes_charge"=>$order->taxes_charge,
                      "payment_method"=>$order->payment_method,
                      "total"=>$order->total,
                      "to_ship"=>$order->to_ship,
                      "shipping_name"=>$order->shipping_first_name,
                      "shipping_address"=>$order->shipping_address,
                      "shipping_city"=>$order->shipping_city,
                      "shipping_pincode"=>$order->shipping_pincode,
                      "billing_name"=>$order->billing_first_name,
                      "billing_address"=>$order->billing_address,
                      "billing_city"=>$order->billing_city,
                      "billing_pincode"=>$order->billing_pincode,
                      "coupon_price"=>$order->coupon_price,
                      "coupon_code"=>$order->coupon_code
                    );
                   $order_status_arr=array();
                   if($order->orderdate!=""){
                        $order_placed=$order->orderdate;
                   }
                   else{
                      $order_placed="";
                   }
                   if($order->pending_datetime!=""){
                        $pending=$order->pending_datetime;
                   }
                   else{
                      $pending="";
                   }
                   if($order->onhold_datetime!=""){
                       $onhold=$order->onhold_datetime;
                   }
                   else{
                      $onhold="";
                   }
                   if($order->processing_datetime!=""){
                       $processing=$order->processing_datetime;
                   }
                   else{
                      $processing="";
                   }
                   if($order->completed_datetime!=""){
                       $complete=$order->completed_datetime;
                   }
                   else{
                      $complete="";
                   }
                   if($order->cancel_datetime!=""){
                       $cancel=$order->cancel_datetime;
                   }
                   else{
                      $cancel="";
                   }
                   if($order->refund_datetime!=""){
                       $refund=$order->refund_datetime;
                   }
                   else{
                      $refund="";
                   }
                   $order_status_arr=array(
                      "order_placed"=>$order_placed,
                      "pending"=>$pending,
                      "processing"=>$processing,
                      "onhold"=>$onhold,
                      "completed_datetime"=>$complete,
                      "cancel_datetime"=>$cancel,
                      "refund"=>$refund
                   );
                 $data=array(
                     "order_date"=>$order->orderdate,
                     "order_id"=>$id,
                     "order_details"=>$order_details,
                     "order_status_details"=>$order_status_arr,
                     "order_status"=>$order->order_status
                 );
                 $response = array("status" => 1, "msg" => "Order Details","data"=>$data); 
        }
        else{
                $response = array("status" => 0, "msg" => "Data Not Found"); 
        }
        return Response::json(array("data"=>$response));

    }

  public function order_history($user_id){
        $data=array();
        $today=Order::whereDate("created_at",date('Y-m-d'))->where("user_id",$user_id)->get();
        $start=date("Y-m-d",strtotime("-1 day")).' 23:59:59';
        $end=date("Y-m-d",strtotime("-8 day")).' 00:00:00'; 
        $lastweek=Order::whereBetween('created_at', [$end,$start])->where("user_id",$user_id)->orderby("id","DESC")->get(); 
        // echo "<pre>";echo $start."=>".$end; print_r($lastweek);exit;
        $last_year=Order::whereDate('created_at',"<",$end)->where("user_id",$user_id)->orderby("id","DESC")->get();
        $main_today=array();
        foreach ($today as $k) {
           $orderdata=OrderData::with('productdata')->where("order_id",$k->id)->get();
           $item=0;
           if(count($orderdata)!=0){
              $image=asset('public/upload/product').'/'.$orderdata[0]->productdata->basic_image;
           }
           else{
              $image="";
           }
           foreach ($orderdata as $t) {
              $item=$item+$t->quantity;
           }
           $arr=array();
           $arr['order_date']=date("dS F",strtotime($k->created_at)); 
           $arr['order_no']=$k->id;
           $arr['item']=$item;
           $arr['bill']=$k->total;
           $arr['order_status']="";
            if($k->order_status=='6'){
                  $arr['order_status']="canceled";
            }else if($k->order_status=='5'){
                   $arr['order_status']="completed";
            }else if($k->order_status=='2'){
                   $arr['order_status']="on hold";
            }else if($k->order_status=='3'){
                   $arr['order_status']="pending";
            }else if($k->order_status=='1'){
                    $arr['order_status']="processing";
            }else if($k->order_status=='7'){
                  $arr['order_status']="refuned";
            }
            else if($k->order_status=='4'){
                  $arr['order_status']="Out For Delivery";
            }
            $arr['image']=$image;
           $main_today[]=$arr;
        }
        $main_week=array();
        foreach ($lastweek as $k) {
           $orderdata=OrderData::with('productdata')->where("order_id",$k->id)->get();
           $item=0;
           if(count($orderdata)!=0){
              $image=asset('public/upload/product').'/'.$orderdata[0]->productdata->basic_image;
           }
           else{
              $image="";
           }
           foreach ($orderdata as $t) {
              $item=$item+$t->quantity;
           }
           $arr=array();
           $arr['order_date']=date("dS F",strtotime($k->created_at)); 
           $arr['order_no']=$k->id;
           $arr['item']=$item;
           $arr['bill']=$k->total;
           $arr['order_status']="";
            if($k->order_status=='6'){
                  $arr['order_status']="canceled";
            }else if($k->order_status=='5'){
                   $arr['order_status']="completed";
            }else if($k->order_status=='2'){
                   $arr['order_status']="on hold";
            }else if($k->order_status=='3'){
                   $arr['order_status']="pending";
            }else if($k->order_status=='1'){
                    $arr['order_status']="processing";
            }else if($k->order_status=='7'){
                  $arr['order_status']="refuned";
            }else if($k->order_status=='4'){
                  $arr['order_status']="Out For Delivery";
            }
            $arr['image']=$image;
           $main_week[]=$arr;
        }
          $remaining=array();
        foreach ($last_year as $k) {
           $orderdata=OrderData::with('productdata')->where("order_id",$k->id)->get();
           $item=0;
           if(count($orderdata)!=0){
              $image=asset('public/upload/product').'/'.$orderdata[0]->productdata->basic_image;
           }
           else{
              $image="";
           }
           foreach ($orderdata as $t) {
              $item=$item+$t->quantity;
           }
           $arr=array();
           $arr['order_date']=date("dS F",strtotime($k->created_at)); 
           $arr['order_no']=$k->id;
           $arr['item']=$item;
           $arr['bill']=$k->total;
           $arr['order_status']="";
            if($k->order_status=='6'){
                  $arr['order_status']="canceled";
            }else if($k->order_status=='5'){
                   $arr['order_status']="completed";
            }else if($k->order_status=='2'){
                   $arr['order_status']="on hold";
            }else if($k->order_status=='3'){
                   $arr['order_status']="pending";
            }else if($k->order_status=='1'){
                    $arr['order_status']="processing";
            }else if($k->order_status=='7'){
                  $arr['order_status']="refuned";
            }else if($k->order_status=='4'){
                  $arr['order_status']="Out For Delivery";
            }
            $arr['image']=$image;
           $remaining[]=$arr;
        }
        $t1=array("name"=>"Today","list"=>$main_today);
        $t2=array("name"=>"Last week","list"=>$main_week);
        $t3=array("name"=>"All","list"=>$remaining);
        if(empty($main_today)&&empty($main_week)&&empty($remaining)){
          $response = array("status" =>0, "msg" => "No Order History");
        }
        else{
          $data[]=$t1;
          $data[]=$t2;
          $data[]=$t3;
          $response = array("status" => 1, "msg" => "Order History","orders"=>$data); 
        }
        
        return Response::json(array("data"=>$response));
    }

}
?>

