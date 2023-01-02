<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App\User;
use App\Model\CartData;
use App\Model\Taxes;
use App\Model\Product;
use App\Model\Shipping;
use DateTimeZone;
use DateTime;
use Image;
use Mail;
use DB;
class CartDataController extends Controller {
    public function __construct() {
         parent::callschedule();
    }
   
    public function addcart(Request $request){
          $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'product_id' => 'required',
                      'qty'=>'required',
                      'product_price'=>'required'              
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'product_id.required' => "product_id is required",
                      'qty.required'=>"qty is required",
                      'product_price.required'=>"product_price is required"
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
                $getdata=CartData::where("user_id",$request->get("user_id"))->where("product_id",$request->get("product_id"))->first();
                 $subtotal=$request->get("qty")*$request->get("product_price");
                  $producttax=Product::find($request->get("product_id"));
                  $taxdata=Taxes::find($producttax->tax_class);
                  $a=$taxdata->rate/100;
                  $b=$subtotal*$a;
                if($getdata){
                    $getdata->option=$request->get("option");
                    $getdata->label=$request->get("label");
                    $getdata->qty=$request->get("qty");
                    $getdata->price_product=$request->get("product_price");
                    $getdata->tax_name=$taxdata->tax_name;
                    $getdata->tax=number_format((float)$b, 2, '.', '');
                    $getdata->save();
                     $id=$getdata->id;
                }
                else{
                $data=new CartData();
                $data->user_id=$request->get("user_id");
                $data->product_id =$request->get("product_id");
                $data->option=$request->get("option");
                $data->label=$request->get("label");
                $data->qty=$request->get("qty");
                $data->price_product=$request->get("product_price");
                $data->tax_name=$taxdata->tax_name;
                $data->tax=number_format((float)$b, 2, '.', '');
                $data->save();
                $id=$data->id;
               
                }
                $gettotal=CartData::where("user_id",$request->get("user_id"))->get();
                $array=array("total"=>count($gettotal),"id"=>$id);
                 $response = array("status" =>1, "msg" => "Cart Add Successfully","data"=>$array);
               
           }
           return Response::json(array("data"=>$response));
    
    }

    public function getcart($id){
       $getcartdata=CartData::with('productdata')->where("user_id",$id)->get();
       if(count($getcartdata)!=0){
           $main_array=array();
           foreach ($getcartdata as $k) {
               $data=array();
               $data['image']=asset('public/upload/product').'/'.$k->productdata->basic_image;
               $data['name']=$k->productdata->name;
               $data['qty']=$k->qty;
               $data['price']=$k->price_product;
               $data['option']=$k->option;
               $data['label']=$k->label;
               $data['product_id']=$k->product_id;
               $data['tax_name']=$k->tax_name;
               $data['tax']=$k->tax;
               $data['cart_id']=$k->id;
               $main_array[]=$data;
           }
            
          $Shipping=Shipping::all();
          $ls=array("cartdata"=>$main_array,"shipping"=>$Shipping);
          $response = array("status" =>1, "msg" => "Cart Get Successfully","data"=>$ls);
       }
       else{
          $response = array("status" =>0, "msg" => "Cart Empty");
       }
       return Response::json(array("data"=>$response));
    }

    public function removecart($cart_id){
       $getcartdata=CartData::find($cart_id);
       if($getcartdata){
           $user_id=$getcartdata->user_id;
          
          $getcartdata->delete();
          $total=CartData::where("user_id",$user_id)->get();
          $response = array("status" =>1, "msg" => "Cart Remove Successfully","data"=>count($total));
       }
       else{
          $response = array("status" =>0, "msg" => "Data Not Found");
       }
       return Response::json(array("data"=>$response));
    }
}
?>

