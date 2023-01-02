<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App\User;
use App\Model\Categories;
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
use App\Model\FeatureProduct;
use App\Model\Wishlist;
use App\Model\OrderResponse;
use App\Model\PaymentMethod;
use App\Model\ResetPassword;
use App\Model\QueryAns;
use App\Model\QueryTopic;
use App\Model\Token;
use App\Model\Coupon;
use DateTimeZone;
use DateTime;
use Image;
use Mail;
use DB;
class productfilterControllercopy extends Controller {
    
  public function productfilter(Request $request){
    $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'category' => 'required',
                      'subcategory'=>'required',
                      'brand'=>'required',
                      'price'=>'required',
                      'discount'=>'required',
                      'ratting'=>'required',
                      'filter'=>'required',
                      'user_id'=>'required',
                      'color'=>'required',
                      'size'=>'required',
                      'coupon_id'=>'required'       
                    ];                    
            $messages = array(
                    'category.required' => "category is required",
                    'subcategory.required' => "subcategory is required",
                    'brand.required' => "brand is required",
                    'price.required' => "price is required",
                    'discount.required' => "discount is required",
                    'ratting.required' => "ratting is required",
                    'filter.required' => "filter is required",
                    'user_id.required'=>"user_id is required",
                    'color.required'=>'color is required',
                    'size.required'=>'size is required',
                    'coupon_id.required'=>'coupon_id is required'
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
              $input = $request->input();
                 
                $category=$request->get("category");
                $subcategory=$request->get("subcategory");
                $brand=$request->get("brand");
                $price=$request->get("price");
                $discount=$request->get("discount");
                $ratting=$request->get("ratting");
                $filter=$request->get("filter");
                $user_id=$request->get("user_id");
                $color=$input['color'];
                $size=$request->get("size");
                $coupon=$request->get("coupon_id");
                $getbrand=$this->getbrandlist($category,$subcategory,$brand,$coupon);
                $getsub=$this->getsublist($category,$subcategory,$brand,$coupon);
                $getsize=$this->getsizls($category,$subcategory,$brand,'size');
                $getcolorls=$this->getcolorls($category,$subcategory,$brand,'color'); 
                $klist=$this->kls($category,$subcategory,$brand,$filter,$ratting,$discount,$price,$color,$size,$coupon);
                  $pricelist=array();
                foreach ($klist as $k) {
                         $option=ProductOption::where("product_id",$k->id)->first();
                         $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                         $k->ratting=round($avgStar);
                         $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
                         $k->wish=count($wish);
                         $re=Review::where("product_id",$k->id)->get();
                         $k->totalreview=count($re);
                         $k->basic_image=asset('public/upload/product/').'/'.$k->basic_image;
                         $k->price=$k->selling_price;
                         $pricelist[]=$k->price;
                         unset($k->selling_price);
                         unset($k->category);
                }
                        if($pricelist){
                            $price=$this->getpricelist($pricelist);
                        }
                  
                  $data=array("subcategory"=>$getsub,"brand"=>$getbrand,"product"=>(object)$klist,"pricelist"=>$price,"color"=>$getcolorls,"size"=>$getsize);
                  if(count($klist)!=0){
                       
                      $response = array(
                        'status' =>1,
                        "details"=>$data
                      );
                  }
                  elseif(count($klist)!=0){
                     $response = array(
                        'status' =>1,
                        "details"=>$data
                      );
                  }
                  elseif(empty($getsub)&&empty($getbrand)&&empty($klist)&&empty($price)&&empty($getcolorls)&&empty($getsize)){
                     $response = array(
                        'status' =>0,
                        "details"=>$data
                      );
                  }
                  elseif($getsub!=""||$getbrand!=""||$klist!=""||$price!=""||$getcolorls!=""||$getsize!=""){
                     $response = array(
                        'status' =>1,
                        "details"=>$data
                      );
                  }
                  
                  else{
                    $response = array(
                        'status' =>0,
                        "details"=>$data
                      );
                  }
                 
            }
      
      return Response::json($response);
  }

   function getpricelist($pricelist){
              sort($pricelist);
              $pricelist=array_values(array_unique($pricelist));
              $totaldived=floor(count($pricelist)/4);
              if($totaldived==1){
                  $a=strlen($pricelist[$totaldived*1])-2;
                  $pricels[]=ceil(($pricelist[($totaldived)-1*1]/pow(10,$a)))*pow(10,$a)."-00";
              } 
              if($totaldived==0){
                 $a=strlen($pricelist[$totaldived*1])-2;
                 $pricels[]=ceil(($pricelist[($totaldived)*1]/pow(10,$a)))*pow(10,$a)."-00";
              }
              if($totaldived!=1&&$totaldived!=0){
                $a=strlen($pricelist[$totaldived*1])-2;
                $pricels[]="0-".ceil(($pricelist[(($totaldived)-1)*1]/pow(10,$a)))*pow(10,$a);
                $a=strlen($pricelist[$totaldived*2])-2;
                $b=ceil(($pricelist[(($totaldived)-1)*1]/pow(10,$a)))*pow(10,$a);
                $pricels[]=$b."-".ceil(($pricelist[(($totaldived)-1)*2]/pow(10,$a)))*pow(10,$a);
                $a=strlen($pricelist[$totaldived*3])-2;
                $b=ceil(($pricelist[(($totaldived)-1)*2]/pow(10,$a)))*pow(10,$a);
                $pricels[]=$b."-".ceil(($pricelist[(($totaldived)-1)*3]/pow(10,$a)))*pow(10,$a);
                $pricels[]=ceil(($pricelist[(($totaldived)-1)*3]/pow(10,$a)))*pow(10,$a)."-00";
              }
              return $pricels;
    }

   public function getbrandlist($category,$subcategory,$brand,$code){
      $listbrand=array();
      if($subcategory=="0"&&$brand=="0"&&$category=="0"){
           $brand=Brand::select('id','brand_name','category_id')->where("is_delete",'0')->get();
           $listbrand=$brand;
      }
      if($subcategory=="0"&&$brand=="0"&&$category!="0"){
          $getsubcategory=Categories::where("parent_category",$category)->where("is_active",'1')->where("is_delete",'0')->get();
          $dt=array();
          if(count($getsubcategory)!=0){
              foreach ($getsubcategory as $ke) {
                 $brand=Brand::where("category_id",$ke->id)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
                 foreach ($brand as $b) {
                   $dt[]=$b;
                 }
              }
              $listbrand=$dt;
          }
      }elseif($subcategory!="0"&&($brand=="0"||$brand!="0")&&$category!="0"){
           $brand=Brand::where("category_id",$subcategory)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
           $listbrand=$brand;
      }elseif($subcategory=="0"&&$brand!="0"&&$category!="0"){
              $getb=Brand::find($brand);
              if($getb){
                 $brand=Brand::where("category_id",$getb->category_id)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
                 $listbrand=$brand;
              }
             
              
      }

          if($code){
                      $getcode=Coupon::find($code);
                      $date=date("Y-m-d");
                      $start_date=date("Y-m-d",strtotime($getcode->start_date)); 
                      $end_date=date("Y-m-d",strtotime($getcode->end_date));
                          if(($date>=$start_date)&&($date<=$end_date)){ 
                              $searls=array();             
                              if($getcode->coupon_on=='0'){
                                   $listcat=array();
                                   $ids=array();
                                   $products=explode(",",$getcode->product);
                                   foreach ($products as $k) {
                                      $pro=Product::find($k);
                                      if($pro){
                                         $ids[]=$pro->brand;
                                      }  
                                   }
                                    $ids=array_values(array_unique($ids));
                                    foreach ($ids as $k) {
                                        $listcat[]=Brand::select('id','brand_name','category_id')->find($k);
                                    }
                                      return $listcat;
                              }                             
                        }
          }
          else{
                return $listbrand;
          }
   }

   public function getsublist($category,$subcategory,$brand,$code){
        $subcat=array();
        if($category!="0"){//11
            $subcat=Categories::where("parent_category",$category)->select('id','name','parent_category')->where("is_delete",'0')->get();
        }       
        elseif($category=="0"&&$subcategory!="0"){//01
           $getpar=Categories::find($subcategory);
           $subcat=Categories::where("parent_category",$getpar->parent_category)->select('id','name','parent_category')->where("is_delete",'0')->get();
        }
        elseif($category=="0"&&$subcategory=="0"&&$brand!="0"){
           $getbrand=Brand::where("brand_name",$brand)->first();
           if($getbrand){
              $getpar=Categories::find($getbrand->category_id);
              $subcat=Categories::where("parent_category",$getpar->parent_category)->select('id','name','parent_category')->where("is_delete",'0')->get();
           }
          
        }
        else{//00
            $subcat=Categories::where("parent_category","!=","0")->select('id','name','parent_category')->where("is_delete",'0')->get();
        }
          if($code){
                      $getcode=Coupon::find($code);
                      $date=date("Y-m-d");
                      $start_date=date("Y-m-d",strtotime($getcode->start_date)); 
                      $end_date=date("Y-m-d",strtotime($getcode->end_date));
                          if(($date>=$start_date)&&($date<=$end_date)){ 
                              $searls=array();             
                              if($getcode->coupon_on=='0'){
                                   $listcat=array();
                                   $ids=array();
                                   $products=explode(",",$getcode->product);
                                   foreach ($products as $k) {
                                      $pro=Product::find($k);
                                      if($pro){
                                         $ids[]=$pro->subcategory;
                                      }  
                                   }
                                    $ids=array_values(array_unique($ids));
                                       
                                       foreach ($ids as $k) {
                                        $listcat[]=Categories::where("id",$k)->select('id','name','parent_category')->where("is_delete",'0')->first();
                                      }
                                   return $listcat;
                              }
                             
                        }
          }
          else{
                return $subcat;
          } 
       
   }
 


  public function kls($category, $subcategory, $brand, $sort, $ratting, $discount, $price, $color, $size,$code)
{

    if ($sort == 2)
    {
        $field = "price";
        $orderby = "ASC";
    }
    elseif ($sort == 3)
    {
        $field = "price";
        $orderby = "DESC";
    }
    elseif ($sort == 4)
    {
        $field = "id";
        $orderby = "DESC";
    }
    else
    {
        $field = "id";
        $orderby = "ASC";
    }

    $product = array();
    $data = array();
    if ($category != "0" && $subcategory == "0" && $brand == "0")
    { //100
        if ($discount == "0" && $ratting == "0" && $price == "0")
        { //000
            if ($color == "0" && $size == "0")
            { //00
              if($code!="0"){                   
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }
                else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }                 
            }
            elseif ($color == "0" && $size != "0")
            { //01
              if($code!="0"){                   
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
                else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
            }
            elseif ($color != "0" && $size == "0")
            { //10
              if($code!="0"){                   
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }
                else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }
               

            }
            else if ($color != "0" && $size != "0")
            { //11
              if($code!="0"){                   
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
                else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
                

            }
        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                  }                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                  if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                  }else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                  } 
                }
                else
                { //11
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                  }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }

                }
                elseif ($color != "0" && $size == "0")
                { //10
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                  }
                }
                else
                { //11
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                  if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                  }
                  else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                  }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                  if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                  }
                }
                else
                { //11
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
              if($code!="0"){
                $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
              }
            }
            elseif ($color == "0" && $size != "0")
            { //01
              if($code!="0"){
                $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
              }
            }
            elseif ($color != "0" && $size == "0")
            { //10
              if($code!="0"){
                $product = Product::where("category", $category)->where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
              }
            }
            else
            { //11
              if($code!="0"){
                $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
              }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                  }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }
                }
                else
                { //11
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                  if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                  }
                  else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                  }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                  if($code!="0"){
                    $product = Product::where("category", $category)->where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }
                }
                else
                { //11
                  if($code!="0"){
                      $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  } 
                  else{
                      $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                  }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }
                }
                else
                { //11
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }
                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
              if($code!="0"){
                $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
              }
            }
            elseif ($color == "0" && $size != "0")
            { //01
              if($code!="0"){
                $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
              }
            }
            elseif ($color != "0" && $size == "0")
            { //10
              if($code!="0"){
                $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
              }
            }
            else
            { //11
              if($code!="0"){
                $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
              }
              else{
                $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
              }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                  }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                  }
                  else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                  }
                }
                else
                { //11
                  if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                  }       

                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category") ->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category") ->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price","basic_image", "selling_price", "discount", "product_color","category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price","basic_image", "selling_price", "discount", "product_color","category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                            $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("category", $category)->where("status", '1') ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                         }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                     }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                       $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10); 
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                      if($code!="0"){
                            $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                      }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                      }
                }
                else
                { //11
                      if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                      }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                      }
                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                 if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting); })->paginate(10);
                 }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting); })->paginate(10);
                 }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%'); })->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%'); })->paginate(10);
                }
            }
            elseif ($color != "0" && $size == "0")
            { //10
                  if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }else{
                     $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                  }
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
                
            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                     if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                     }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                     }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                     }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                     }
                }
                else
                { //11
                     if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                     }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                     }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                      if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);

                      }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);

                      }
                    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size .'%');})->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size .'%');})->paginate(10);
                     }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                      if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                      }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                      }
                }
                else
                { //11
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size .'%');})->where("product_color", $color)->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size .'%');})->where("product_color", $color)->paginate(10);
                     }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                     }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->paginate(10);

                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                     } 
                }
                else
                { //11
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->where("product_color", $color)->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->where("product_color", $color)->paginate(10);
                     }
                }

            }
        }
    }
    if ($category != "0" && $subcategory == "0" && $brand != "0")
    { //101
        if ($discount == "0" && $ratting == "0" && $price == "0")
        { //000
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                  if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" .'%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }else{
                     $product = Product::where("category", $category)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" .'%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                  }
            }
            elseif ($color != "0" && $size == "0")
            { //10
                 if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                 }else{
                    $product = Product::where("category", $category)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                 }
            }
            else if ($color != "0" && $size != "0")
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name','like', '%' . "size" .'%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name','like', '%' . "size" .'%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }  
            }
        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }
                   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                     if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                     }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                     }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }   
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                     }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                     if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                     }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                     }
                }
                else
                { //11
                      if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                      }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                      }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }    
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    }   
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }      
                }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                 if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                 }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                 }    
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                } 
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }  
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }   
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }             
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    } 
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)
                        ->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }  
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }                 
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);

                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" .'%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" .'%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);

                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }    
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                }             
            }
            else
            { //11
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }  
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where('name', 'LIKE', '%'. $search . '%')->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where('name', 'LIKE', '%'. $search . '%')->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }   
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }                    
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where('name', 'LIKE', '%' . $search. '%')->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q)use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' .$ratting);})->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where('name', 'LIKE', '%' . $search. '%')->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q)use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' .$ratting);})->paginate(10);
                }     
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size". '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size". '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }                
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }        
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->where("product_color", $color)->paginate(10);
                }        
            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);                    
                  }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }                
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }      
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }                     
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }                   
                }
            }
        }
    }
    if ($category != "0" && $subcategory != "0" && $brand == "0")
    { //110
        if ($discount == "0" && $ratting == "0" && $price == "0")
        {
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id","name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id","name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){}else{}
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id","name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id","name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id","name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }
            }
            else if ($color != "0" && $size != "0")
            { //11
                if($code!="0"){}else{}
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id","name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
            }
        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }     
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }       
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name','like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name','like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }  
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    }        
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }     
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP","price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }       
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where('name', 'LIKE', '%' . $search. '%')->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where('name', 'LIKE', '%' . $search. '%')->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->where("product_color", $color)->paginate(10);
                }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }     
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }      
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }   
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }  
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }   
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }  
                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);

                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);

                }             
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color","category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color","category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                }  
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }       
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }  
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }      
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }   
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("subcategory", $subcategory)->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("subcategory", $subcategory)->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    }   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }  
                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }  
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted",'0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }   
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    } 
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount","<=", $discount)->where("product_color", $color)->paginate(10);
                     }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    } 
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount","<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);}else{$product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
    }

    if ($category != "0" && $subcategory != "0" && $brand != "0")
    { //111
        if ($discount == "0" && $ratting == "0" && $price == "0")
        { //000
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                } 
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }  
            }
            else if ($color != "0" && $size != "0")
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("brand", $brand)->where("subcategory", $subcategory)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                } 
            }
        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }  
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                    } 
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }   
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }         
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q)use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }    
                 }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where('name', 'LIKE', '%' . $search . '%')->where("status", '1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where('name', 'LIKE', '%' . $search . '%')->where("status", '1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q)use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }    
                }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status",'1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size". '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size". '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                } 
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }  
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status",'1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size". '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size". '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }          
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }    
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }  
                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }    
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' ."size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }  
                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status",'1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status",'1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                }   
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("status",'1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                } 
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }  
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    } 
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }    
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }                
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("subcategory", $subcategory)->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("subcategory", $subcategory)->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }   
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q)use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' .$ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')->where("subcategory", $subcategory)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q)use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' .$ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
                

            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("category", $category)->whereHas('couponproductdata',function ($q) use ($code){$q->where('coupon_id', $code);})->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("category", $category)->where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q)use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' .$ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    } 
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' .$ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' .$ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->paginate(10);
                    }  
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field,$orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory",$subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->where("product_color", $color)->paginate(10);
                    }    
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("brand", $brand)->orderby($field, $orderby)->where("subcategory", $subcategory)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }  
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }  
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("category", $category)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name","MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("category", $category)->where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }  
                }
            }
        }
    }
    if ($category == "0" && $subcategory == "0" && $brand == "0")
    { //000
        if ($discount == "0" && $ratting == "0" && $price == "0")
        { //000
            if ($color == "0" && $size == "0")
            { //00                  
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }
                else{
                      $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }   
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                } 
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                } 
            }
            else if ($color != "0" && $size != "0")
            { //11
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
            }
        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    } 
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    } 
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    } 
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }  
                }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                     $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                     $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                } 
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }  
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field,$orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                } 
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount","product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    } 
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function($q) use ($ratting){ $q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    } 
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls',function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                     $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                }
            }
            else
            { //11
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    } 
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);
                    } 
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q)use ($size){$q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                    $product = Product::where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                     $product = Product::where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }
            }
            else
            { //11
                if($code!="0"){
                     $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("discount", "<=", $discount)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '. $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    } 
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})>orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }   
                }
            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting); })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
    }
    if ($category == "0" && $subcategory == "0" && $brand != "0")
    { //001
        if ($discount == "0" && $ratting == "0" && $price == "0")
        { //000
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }else{
                     $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->paginate(10);
                }
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status",'1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("product_color", $color)->paginate(10);
                }
            }
            else if ($color != "0" && $size != "0")
            { //11
                if($code!="0"){
                     $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
            }
        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->paginate(10);
                    }   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{}
                    $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image","selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                    }else{}
                     $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->paginate(10);
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image","selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image","selling_price", "discount")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){ $q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size. '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image","selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                }
                    }    

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image","selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' .$size . '%');})->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                    }   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);
                    } 
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->where("product_color", $color)->paginate(10);
                    }
                }
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                }else{
                    $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                } 
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status",'1') ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }else{
                    $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size){$q->where('name', 'like', '%' . "size" . '%')->where('label', 'like', '%' . $size . '%');})->paginate(10);
                }  
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->where("product_color", $color)->paginate(10);
                }
            }
            else
            { //11
                if($code!="0"){}else{}
            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image","selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = ' . $ratting);})->paginate(10);
                    }
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")->where("is_deleted", '0')->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting){$q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);})->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                   
                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }
                    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }
                    
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->paginate(10);

                }else{
                     $product = Product::where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->paginate(10);

                }
               
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                     $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }else{
                     $product = Product::where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }
               
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                }else{
                    $product = Product::where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                }
                
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }
                

            }

        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{}
                     $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                   
                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);

                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);

                    }
                    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);

                }else{
                    $product = Product::where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);

                }
                
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }else{
                    $product = Product::where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }
                
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);
                }
                

            }
            else
            { //11
                if($code!="0"){
                     $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }else{
                     $product = Product::where("discount", "<=", $discount)->where("brand", $brand)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }
               

            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }
                   
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                   
                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("brand", $brand)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("brand", $brand)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }

            }
        }
    }
    if ($category == "0" && $subcategory != "0" && $brand == "0")
    { //010
        if ($discount == "0" && $ratting == "0" && $price == "0")
        { //000
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->paginate(10);
                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->paginate(10);
                }
                

            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }
                
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("product_color", $color)->paginate(10);
                }
                

            }
            else if ($color != "0" && $size != "0")
            { //11
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);

                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);

                }
                
            }

        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                    }else{}
                     $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);
                   

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);

                    }
                    
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                    
                }

            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);
                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);
                }
                

            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }

                
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);

                }else{
                     $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);

                }
               
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }
                

            }

        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }
                   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                    
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }
                   
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->paginate(10);
                }else{
                     $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->paginate(10);
                }
               

            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                     $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }else{
                     $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }
               

            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                }
                
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }
                

            }

        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                    }
                    
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);

                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);

                    }
                   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                    }
                   
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);

                    }
                    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);
                }else{
                    $product = Product::where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);
                }
                

            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }else{
                    $product = Product::where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }
                

            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);
                }
                

            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);

                }else{
                    $product = Product::where("discount", "<=", $discount)->where("subcategory", $subcategory)->where("status", '1')
                    ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);

                }
                
            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }
                    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                    
                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                    
                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("subcategory", $subcategory)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("subcategory", $subcategory)->where("status", '1')
                        ->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }

            }
        }
    }
    if ($category == "0" && $subcategory != "0" && $brand != "0")
    { //011
        if ($discount == "0" && $ratting == "0" && $price == "0")
        { //000
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->paginate(10);
                }else{
                     $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->paginate(10);
                }
               

            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }
                

            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("product_color", $color)->paginate(10);

                }else{
                     $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("product_color", $color)->paginate(10);

                }
               
            }
            else if ($color != "0" && $size != "0")
            { //11
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }
                
            }

        }
        else if ($discount == "0" && $ratting == "0" && $price != "0")
        { //001
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                    
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("product_color", $color)->paginate(10);

                    }
                   
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                    
                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("product_color", $color)->paginate(10);

                    }
                    
                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})>where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("product_color", $color)->paginate(10);

                    }
                   
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }

            }
        }
        elseif ($discount == "0" && $ratting != "0" && $price == "0")
        { //010
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);
                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);
                }
                

            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);

                }
                
            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);

                }else{
                     $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);

                }
               
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);

                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);

                }
                
            }

        }
        elseif ($discount == "0" && $ratting != "0" && $price != "0")
        { //011
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);

                    }
                   
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                    
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }
                    
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                    
                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);

                    }
                    
                }
                else
                { //11
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
            }
        }
        elseif ($discount != "0" && $ratting == "0" && $price == "0")
        { //100
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                     $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->paginate(10);

                }else{
                     $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->paginate(10);

                }
               
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->paginate(10);
                }
                

            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                }
                
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')
                    ->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }
                

            }

        }
        elseif ($discount != "0" && $ratting == "0" && $price != "0")
        { //101
            if($code!="0"){}else{}
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                   

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }

                   
                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->paginate(10);

                    }
                    
                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                   
                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);
                    }
                   

                }
            }
        }
        elseif ($discount != "0" && $ratting != "0" && $price == "0")
        { //110
            if ($color == "0" && $size == "0")
            { //00
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);

                }else{
                    $product = Product::where("discount", "<=", $discount)->where("status", '1')
                    ->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->paginate(10);

                }
                
            }
            elseif ($color == "0" && $size != "0")
            { //01
                if($code!="0"){}else{}
                

            }
            elseif ($color != "0" && $size == "0")
            { //10
                if($code!="0"){
                     $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);

                }else{
                     $product = Product::where("discount", "<=", $discount)->where("status", '1')
                    ->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->where("product_color", $color)->paginate(10);

                }
               
            }
            else
            { //11
                if($code!="0"){
                    $product = Product::where("discount", "<=", $discount)->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("status", '1')
                    ->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }else{
                    $product = Product::where("discount", "<=", $discount)->where("status", '1')
                    ->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                    ->where("is_deleted", '0')->whereHas('rattingdata', function ($q) use ($ratting)
                {
                    $q->groupBy('ratting')
                        ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                })->whereHas('optionls', function ($q) use ($size)
                {
                    $q->where('name', 'like', '%' . "size" . '%')
                        ->where('label', 'like', '%' . $size . '%');
                })->where("product_color", $color)->paginate(10);
                }
                

            }
        }
        else
        { //111
            $str = explode("-", $price);
            if ($str[0] == "0")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                          $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                          $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                  

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("product_color", $color)->paginate(10);
                    }
                    

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", "<=", $str[1])->where("discount", "<=", $discount)->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                   
                }

            }
            elseif ($str[1] == "00")
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }
                   

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);
                    }
                    

                }
                elseif ($color != "0" && $size == "0")
                { //10
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);
                    }
                   

                }
                else
                { //11
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->where("selling_price", ">=", $str[0])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                    }
                   
                }

            }
            else
            {
                if ($color == "0" && $size == "0")
                { //00
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->paginate(10);
                    }
                    

                }
                elseif ($color == "0" && $size != "0")
                { //01
                    if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->paginate(10);

                    }
                    
                }
                elseif ($color != "0" && $size == "0")
                { //10 if($search!="0"){
                    if($code!="0"){
                         $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                    }else{
                         $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->where("product_color", $color)->paginate(10);

                    }
                   
                }
                else
                { //11
                     if($code!="0"){
                        $product = Product::where("status", '1')->whereHas('couponproductdata', function ($q) use ($code){$q->where('coupon_id', $code);})->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                     }else{
                        $product = Product::where("status", '1')->where("subcategory", $subcategory)->where("brand", $brand)->orderby($field, $orderby)->select("id", "name", "MRP", "price", "basic_image", "selling_price", "discount", "product_color", "category")
                        ->where("is_deleted", '0')
                        ->whereBetween("selling_price", [$str[0], $str[1]])->whereHas('rattingdata', function ($q) use ($ratting)
                    {
                        $q->groupBy('ratting')
                            ->havingRaw('round(AVG(ratting)) = ' . $ratting);
                    })->where("discount", "<=", $discount)->whereHas('optionls', function ($q) use ($size)
                    {
                        $q->where('name', 'like', '%' . "size" . '%')
                            ->where('label', 'like', '%' . $size . '%');
                    })->where("product_color", $color)->paginate(10);

                     }

                    
                }

            }
        }
    }

    return $product;
}
  
  
   public function getcolorls($category,$subcategory,$brand){
       $product=array();
        $colorls=array();
        if($category!=0&&$subcategory==0&&$brand==0){//100
         $product=Product::where("category",$category)->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->where('status','1')->get();
          }else if($category!=0&&$subcategory==0&&$brand!=0){//101
              $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
          }else if($category!=0&&$subcategory!=0&&$brand==0){//110
            $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
          }else if($category!=0&&$subcategory!=0&&$brand!=0){//111
              $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("is_deleted",'0')->where("brand",$brand)->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
          }
          else if($category==0&&$subcategory==0&&$brand==0){//000
              $product=Product::where("status",'1')->where("is_deleted",'0')->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
          }
          else if($category==0&&$subcategory!=0&&$brand==0){//010
            $product=Product::where("subcategory",$subcategory)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
          }
          else if($category==0&&$subcategory!=0&&$brand!=0){//011
              $product=Product::where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
          }
           else if($category==0&&$subcategory==0&&$brand!=0){//001
              $product=Product::where("brand",$brand)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
          }
        foreach ($product as $k) {
              $arr=array();
              $arr["code"]=$k->product_color;
              $arr["name"]=$k->color_name;
              $colorls[]=$arr;
         }
         $new = [];
         foreach ($colorls as $item) {
             if (empty($new[$item['code']])) {
                 if($item['code']!=null){
                    $new[$item['code']] = ['code' => $item['code'],"name"=>$item['name']]; 
                 }
             }
         }
         $new = array_values($new);
         return $new;
   }

   function getsizls($category,$subcategory,$brand,$fields){
    $colorls=array();
      $product=array();
        if($category!=0&&$subcategory==0&&$brand==0){//100
         $product=Product::with('optionls')->where("category",$category)->where("is_deleted",'0')->where("status",'1')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory==0&&$brand!=0){//101
          $product=Product::with('optionls')->where("category",$category)->where("brand",$brand)->where("status",'1')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory!=0&&$brand==0){//110
        $product=Product::with('optionls')->where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory!=0&&$brand!=0){//111
          $product=Product::with('optionls')->where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }
      elseif($category==0&&$subcategory!=0&&$brand!=0){//011
          $product=Product::with('optionls')->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }
      elseif($category==0&&$subcategory!=0&&$brand==0){//010
          $product=Product::with('optionls')->where("subcategory",$subcategory)->where("status",'1')->where("is_deleted",'0')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }
      elseif($category==0&&$subcategory==0&&$brand!=0){//001
          $product=Product::with('optionls')->where("brand",$brand)->where("status",'1')->where("is_deleted",'0')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }
      else{//000
        $product=Product::with('optionls')->where("status",'1')->where("is_deleted",'0')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }
    
         foreach ($product as $k) {
            $str=explode(",",strtoupper($k->optionls->name));
          foreach ($str as $kt=>$val) {
              if(strstr($val,strtoupper($fields))==true){
                      $name=$kt;
              }
          }
             $value=explode("#",$k->optionls->label);
             $colorarr=explode(",",$value[$name]);
             
             foreach ($colorarr as $co) {
              $colorls[]=$co;
             }
             
         }
       return array_values(array_unique($colorls));
   }
 
   
}

?>

