<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Model\Coupon;
use App\Model\Order;
use App\Model\Product;
use Auth;
Use Image;
use Hash;
use Cart;
class CouponController extends Controller {
      public function __construct() {
         parent::callschedule();
    }
      public function index(){
           return view("admin.coupon.default");
      }

      public function checkcoupon(Request $request){
          return $this->verifiedcoupon($request->get("coupon"));
      }

      public function coupondatatable(){
            $coupon =Coupon::orderBy('id','DESC')->where("is_deleted",'0')->get();
            return DataTables::of($coupon)
                ->editColumn('id', function ($coupon) {
                   return $coupon->id;
                })
                ->editColumn('name', function ($coupon) {
                   return $coupon->name;
                })           
                ->editColumn('code', function ($coupon) {
                    return $coupon->code;            
                })
                 ->editColumn('date', function ($coupon) {
                    if($coupon->start_date!=""){
                        return $coupon->start_date."-".$coupon->end_date;
                    }
                    else{
                        return "";
                    }
                   
                })
                ->editColumn('value', function ($coupon) {
                    if($coupon->discount_type=='0'){
                        return $coupon->value;
                    }
                    if($coupon->discount_type=='1'){
                        return $coupon->value."%";
                    }
                    return '';
                })           
                ->editColumn('action', function ($coupon) {
                     $edit=url('admin/editcoupon',array('id'=>$coupon->id));
                     $delete=url('admin/deletecoupon',array('id'=>$coupon->id));
                     $return = '<a href="'.$edit.'" rel="tooltip" title="active" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $delete. "'" . ')" rel="tooltip" title="Delete Category" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';     
                     return $return;      
                })           
            ->make(true);
      }
   
      public function addcoupon(){
         return view("admin.coupon.addcoupon");
      }

      public function savecoupon(Request $request){        
          if($request->get("id")==0||$request->get("id")==""){
                $checkcoupon=Coupon::where("code",$request->get("code"))->first();
                if(!empty($checkcoupon)){
                    return "Code";
                }
                $data=new Coupon();
          }
          else{
                
                $data=Coupon::find($request->get("id"));
          }
          $data->name=$request->get("name");
          $data->code=$request->get("code");
          $data->discount_type=$request->get("discount_type");
          $data->value=$request->get("value");
          $data->start_date=$request->get("start_date");
          $data->end_date=$request->get("end_date");
          $data->free_shipping=$request->get("free_shipping");
          $data->status=$request->get("status");
          $data->save();
          return $data->id;
      }

      public function savecouponsecondstep(Request $request){
            $data=Coupon::find($request->get("id"));
            $data->minmum_spend=$request->get("minmum_send");
            $data->maximum_spend=$request->get("maximum_spend");
            $data->product=$request->get("product");
            $data->categories=$request->get("category");
            $data->coupon_on=$request->get("coupon_on");
            $data->save();
            return $data->id;
      }

      public function savecouponstepthree(Request $request){
            $data=Coupon::find($request->get("id"));
            $data->usage_limit_per_coupon=$request->get("per_coupon");
            $data->usage_limit_per_customer=$request->get("per_customer");
            $data->save();
            return $data->id;
      }

      public function editcoupon($id){
        $data=Coupon::find($id);
        return view("admin.coupon.addcoupon")->with("data",$data);
      }
      public function verifiedcoupon($coupon_code){
         $date=date("Y-m-d");
         $data=Coupon::where("code",$coupon_code)->where("status",'1')->first();
         if(!$data){
               return 0;
         }
         else{
                $start_date=date("Y-m-d",strtotime($data->start_date)); 
                $end_date=date("Y-m-d",strtotime($data->end_date));
                if(($date>=$start_date)&&($date<=$end_date)){
                        $order=Order::where("coupon_code",$coupon_code)->get();
                        $orderuser=Order::where("coupon_code",$coupon_code)->where("user_id",Auth::id())->get();
                        if($data->usage_limit_per_coupon!=""&&($data->usage_limit_per_coupon<count($order))){
                             return 0;
                        }
                        elseif($data->usage_limit_per_customer!=""&&($data->usage_limit_per_customer<=count($orderuser))){
                             return 0;
                        }
                        elseif($data->minmum_spend!=""&&$data->minmum_spend>=Cart::getTotal()){
                             return 0;
                        }
                        elseif($data->maximum_spend!=""&&$data->maximum_spend<=Cart::getTotal()){
                             return 0;
                        }
                        else{
                          
                              $temp=0;
                              $cartCollection = Cart::getContent();
                              foreach ($cartCollection as $item) {
                                $arr[]=$item->id;
                              }
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
                                   $discount=(Cart::getTotal()*$data->value)/100;
                              }
                              else{
                                 $discount=$data->value;
                              }
                                 return $data;
                              }
                              else{
                               return 0;
                              }
                             
                           }
                        }
                        else{
                           return 0;
                        }
                      }
          
   }

   public function deletecoupon($id){
      $get=Coupon::find($id);
      $get->is_deleted='1';
      $get->save();
      Session::flash('message',__('messages_error_success.Coupon_Delete')); 
      Session::flash('alert-class', 'alert-success');
      return redirect()->back();
   }

}