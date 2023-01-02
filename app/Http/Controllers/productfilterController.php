<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Model\Categories;
use App\Model\Product;
use App\Model\ProductAttributes;
use App\Model\ProductOption;
use App\Model\OrderData;
use App\Model\Review;
use App\Model\Wishlist;
use App\Model\Brand;
use App\Model\Setting;
use App\Model\Coupon;
use App\Model\Shipping;
use Auth;
Use Image;
use Hash;
use DB;
class productfilterController extends Controller {
    public function __construct() {
         parent::callschedule();
          $setting=Setting::find(1);
         $shiping=Shipping::find(1);
         $res_curr=explode("-",$setting->default_currency);
         Session::put("site_address",$setting->address);   
         Session::put("site_email",$setting->email);
         Session::put("site_phone",$setting->phone);
         Session::put("site_workinghour",$setting->working_day);
         Session::put("site_mainfeature",$setting->main_feature);
         Session::put("site_newsletter",$setting->newsletter);
         Session::put("currency",$res_curr[1]);
         Session::put("google_active",$setting->google_active);
         Session::put("facebook_active",$setting->facebook_active);
         if(Session::get("site_color")==""){
             Session::put("site_color","#f07f13");
             Session::put("colorid",'1');
        }
        if($shiping){
          Session::put("home_delivery",$shiping->id."#".$shiping->cost);
        }
        else{
          Session::put("home_delivery","0#0");
        }
    }
   public function productls(Request $request,$category_id,$subcategory_id,$brand_id,$discount){
    if($request->get("ca")!=""){
        $category_id=$request->get("ca");
    }
      
      $search=$request->get("s");
      $code=$request->get("cd");
      if($category_id=="0"&&$subcategory_id=="0"&&$brand_id=="0"){//000
          if($discount=="0"){
            if($search!=""){
              $product=Product::where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->where('name', 'LIKE', '%' . $search . '%')->get();
            }
            else{
                 $product=Product::where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->get();
            }
          }
          else{
                if($search!=""){
                $product=Product::where("status",'1')->orderby("discount",'DESC')->where('name', 'LIKE', '%' . $search . '%')->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                }
                else{
                      $product=Product::where("status",'1')->orderby("discount",'DESC')->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                }
            
           }
      }
      if($category_id=="0"&&$subcategory_id=="0"&&$brand_id!="0"){//001
         if($discount=="0"){
              if($search!=""){
                  $product=Product::where("brand",$brand_id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->get();
              }
              else{
                  $product=Product::where("brand",$brand_id)->where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby("id",'DESC')->where("is_deleted",'0')->get();
              }              
         }else{
              if($search!=""){
                  $product=Product::where("brand",$brand_id)->where("status",'1')->orderby("discount",'DESC')->where("is_deleted",'0')->where("discount","<=",$discount)->get();
              }
              else{
                  $product=Product::where("brand",$brand_id)->where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby("discount",'DESC')->where("is_deleted",'0')->where("discount","<=",$discount)->get();
              }              
         }
      }
      if($category_id=="0"&&$subcategory_id!="0"&&$brand_id=="0"){// 010
          if($discount==0){
              if($search!=""){
                  $product=Product::where("subcategory",$subcategory_id)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("is_deleted",'0')->orderby("id",'DESC')->get();
              }
              else{
                   $product=Product::where("subcategory",$subcategory_id)->where("status",'1')->where("is_deleted",'0')->orderby("id",'DESC')->get();
              }            
          }else{
             if($search!=""){
                $product=Product::where("subcategory",$subcategory_id)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("is_deleted",'0')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
              }
              else{
                $product=Product::where("subcategory",$subcategory_id)->where("status",'1')->where("is_deleted",'0')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
              }            
          }
      }
      if($category_id=="0"&&$subcategory_id!="0"&&$brand_id!="0"){//011
          if($discount=="0"){
             if($search!=""){
                  $product=Product::where("subcategory",$subcategory_id)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("id",'DESC')->get();
              }
              else{
                  $product=Product::where("subcategory",$subcategory_id)->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("id",'DESC')->get();
              }
          }
          else{
             if($search!=""){
                   $product=Product::where("subcategory",$subcategory_id)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
              }
              else{
                   $product=Product::where("subcategory",$subcategory_id)->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
              }             
          }
     }
     if($category_id!="0"&&$subcategory_id=="0"&&$brand_id=="0"){//100
         if($discount=="0"){
             if($search!=""){              
                $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("is_deleted",'0')->orderby("id",'DESC')->get();
             }
             else{
                 $product=Product::where("category",$category_id)->where("status",'1')->where("is_deleted",'0')->orderby("id",'DESC')->get();
             }
         }
         else{
             if($search!=""){
                $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("is_deleted",'0')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
             }
             else{
                $product=Product::where("category",$category_id)->where("status",'1')->where("is_deleted",'0')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
             }
              
         }
      }
      if($category_id!="0"&&$subcategory_id=="0"&&$brand_id!="0"){//101
          if($discount=="0"){
             if($search!=""){
                  $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("id",'DESC')->get();
              }
              else{
                  $product=Product::where("category",$category_id)->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("id",'DESC')->get();
              }
          }
          else{
              if($search!=""){
                  $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
              }
              else{
                $product=Product::where("category",$category_id)->where("brand",$brand_id)->where("is_deleted",'0')->where("status",'1')->orderby("discount",'DESC')->where("discount","<=",$discount)->get();
              }              
          }
     }
     if($category_id!="0"&&$subcategory_id!="0"&&$brand_id=="0"){//110
        if($discount=="0"){
              if($search!=""){
                   $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory_id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->where("discount","<=",$discount)->get();
              }
              else{
                   $product=Product::where("category",$category_id)->where("subcategory",$subcategory_id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->where("discount","<=",$discount)->get();
              }           
        }
        else{
           if($search!=""){
                   $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory_id)->where("status",'1')->orderby("discount",'DESC')->where("is_deleted",'0')->get();
              }
              else{
                   $product=Product::where("category",$category_id)->where("subcategory",$subcategory_id)->where("status",'1')->orderby("discount",'DESC')->where("is_deleted",'0')->get();
              }
        }
     }
     if($category_id!="0"&&$subcategory_id!="0"&&$brand_id!="0"){//111
         if($discount=="0"){
           if($search!=""){
                   $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory_id)->where("brand",$brand_id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->get();
              }
              else{
                     $product=Product::where("category",$category_id)->where("subcategory",$subcategory_id)->where("brand",$brand_id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->get();
              }
           
         }
         else{
           if($search!=""){
                   $product=Product::where("category",$category_id)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory_id)->where("brand",$brand_id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->where("is_deleted",'0')->get();
              }
              else{
                     $product=Product::where("category",$category_id)->where("subcategory",$subcategory_id)->where("brand",$brand_id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->where("is_deleted",'0')->get();
              }
           
         }
     }
     $pricelist=array();
    
     if($code!=""){
            $getcode=Coupon::where("code",$code)->first();
            $date=date("Y-m-d");
            $start_date=date("Y-m-d",strtotime($getcode->start_date)); 
            $end_date=date("Y-m-d",strtotime($getcode->end_date));
            if(($date>=$start_date)&&($date<=$end_date)){ 
                $searls=array();             
                if($getcode->coupon_on=='0'){//product
                    foreach ($product as $k) {
                      $products=explode(",",$getcode->product);
                      if(in_array($k->id,$products)){
                            $k->name=substr($k->name,0,15);
                            $getreview=Review::where("product_id",$k->id)->get();
                            $k->total_review=count($getreview);
                            $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                            $k->avgStar=round($avgStar);
                            $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
                            $k->wish=count($wish);
                            $pricelist[]=$k->price;
                            $k->disper=$k->discount;
                            $k->price=$k->selling_price;   
                            $searls[]=$k;
                      }
                    }
                }
                if($getcode->coupon_on=='1'){//category

                   $categorypro=explode(",", $getcode->categories);

                    foreach ($product as $k) {
                        if(in_array($k->category,$categorypro)){
                            $k->name=substr($k->name,0,15);
                            $getreview=Review::where("product_id",$k->id)->get();
                            $k->total_review=count($getreview);
                            $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                            $k->avgStar=round($avgStar);
                            $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
                            $k->wish=count($wish);
                            $pricelist[]=$k->price;
                            $k->disper=$k->discount;
                            $k->price=$k->selling_price; 
                             $searls[]=$k;
                        }
                    }
                }
                $product=$searls;
            }
     }
     else{
         foreach ($product as $k) {
            $k->name=substr($k->name,0,15);
            $getreview=Review::where("product_id",$k->id)->get();
            $k->total_review=count($getreview);
            $avgStar = Review::where("product_id",$k->id)->avg('ratting');
            $k->avgStar=round($avgStar);
            $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
            $k->wish=count($wish);
            $pricelist[]=$k->price;
            $k->disper=$k->discount;
            $k->price=$k->selling_price;   
        }
     }
   
       
        $pricels=array();
        if(!empty($pricelist)){
             $pricels=$this->getpricelist($pricelist);
        }

        $getsub=$this->getsubcat($category_id,$subcategory_id);

        $brand=array();
        foreach ($getsub as $sub) {
            if($subcategory_id!=0){
                if($sub->id==$subcategory_id){
                    $getbrand=Brand::where("category_id",$sub->id)->where('is_delete','0')->get();
                    foreach ($getbrand as $ge) {
                        $brand[]=$ge->brand_name;
                        $category_id=$sub->parent_category;
                    }
                }
            }
            else{
                $getbrand=Brand::where("category_id",$sub->id)->where('is_delete','0')->get();
                foreach ($getbrand as $ge) {
                    $brand[]=$ge->brand_name;
                }
               
              }
        }
       
        $bname="";
        $brandname=Brand::find($brand_id);
        if(isset($brandname->brand_name)){
           $bname=$brandname->brand_name;
        }
        $catgyname=Categories::find($category_id);
        $brand=array_values(array_unique($brand));        
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist(); 
        $getcolorls=$this->getcolorls($category_id,$subcategory_id,$brand_id,'color');
        $getsize=$this->getsizls($category_id,$subcategory_id,$brand_id,'size');
        $mywish=Wishlist::where("user_id",Auth::id())->get();   
        $setting=Setting::find(1);
        return view("user.product.productlist")->with("header_menu",$getcat)->with("productdata",$productdata)->with("productlist",$product)->with("lssub",$getsub)->with("brandls",$brand)->with("subcategory",$subcategory_id)->with("brand",$bname)->with("categorydata",$catgyname)->with("mywish",$mywish)->with("pricels",$pricels)->with("discount",$discount)->with("colorarr",$getcolorls)->with("sizearr",$getsize)->with("setting",$setting);
   }
    public function getheadermenu(){
        $main_array=array(); 
        $category=Categories::where("parent_category",'0')->where('is_delete','0')->get();
        foreach ($category as $ke) {          
            $subcategory=Categories::where("parent_category",$ke->id)->where('is_delete','0')->get();            
            $sub_arr=array();
            foreach ($subcategory as $sub) {    
                $brand=Brand::where("category_id",$sub->id)->where('is_delete','0')->get();
                $sub->brand=$brand;
                $sub_arr[]=$sub;
            }
            $ke->subcategory=$sub_arr;
            $main_array[]=$ke;
        }
        return $main_array;
    }
    public function getproductlist(){
        $product=Product::all();
        return $product;
    }
    function headreadMoreHelper($story_desc, $chars =35) {
        $story_desc = substr($story_desc,0,$chars);  
        $story_desc = substr($story_desc,0,strrpos($story_desc,' '));  
        $story_desc = $story_desc;  
        return $story_desc;  
    }  
    public function getsubcat($category_id,$subcategory_id){
        $getsubcat=array();
        if($category_id==0&&$subcategory_id==0){//00
           $getsubcat=Categories::where('parent_category','!=','0')->where('is_delete','0')->get();
        }elseif($category_id==0&&$subcategory_id!=0){//01
           $getparent=Categories::where('parent_category','!=','0')->where("id",$subcategory_id)->where('is_delete','0')->first();
           if($getparent){
            $getsubcat=Categories::where('parent_category',$getparent->parent_category)->where('is_delete','0')->get();
           }
           
        }elseif($category_id!=0&&$subcategory_id==0){//10
           $getsubcat=Categories::where('parent_category',$category_id)->where('is_delete','0')->get();
        }
        else{//11
            $getsubcat=Categories::where('parent_category',$category_id)->where('is_delete','0')->get();
        }
        return $getsubcat;
    }

    function getpricelist($pricelist){
              sort($pricelist);
              $pricelist=array_values(array_unique($pricelist));
              $totaldived=floor(count($pricelist)/4);
              if($totaldived==1){
                  $a=strlen($pricelist[$totaldived*1])-2;
                  $pricels[]=ceil(($pricelist[($totaldived)-1*1]/pow(10,$a)))*pow(10,$a)."-Above";
              } 
              if($totaldived==0){
                 $a=strlen($pricelist[$totaldived*1])-2;
                 $pricels[]=ceil(($pricelist[($totaldived)*1]/pow(10,$a)))*pow(10,$a)."-Above";
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
                $pricels[]=ceil(($pricelist[(($totaldived)-1)*3]/pow(10,$a)))*pow(10,$a)."-Above";
              }
              return $pricels;
    }

    public function changeproductdata(Request $request){
            $subcategory=$request->get("subcategory");
            $category=$request->get("category");
            $brand=$request->get("brand");
            $price=$request->get("price");
            $ratting=$request->get("ratting");
            $sort=$request->get("sorttype");
            $discount=$request->get("discount"); 
            $color=$request->get("color"); 
            $size=$request->get("size");  
            $code=$request->get("code");    
            $search=$request->get("search");     
            $getbrand=$this->getbrandlist($category,$subcategory,$brand);

             if(!empty($getbrand)){
                $findbrand=Brand::where("brand_name",$request->get("brand"))->get();
                $temp=0;
                if(count($findbrand)!=0){
                  foreach ($getbrand as $k) {
                     foreach ($findbrand as $fd) {
                        if($fd->id==$k->id){
                           $temp=1;
                           $brand=$fd->id;
                        }
                    }
                  }
                  if($temp==0){
                    $brand=0;
                  }
                }
            }

            
            $getcolorls=$this->getcolorls($category,$subcategory,$brand,'color');


            $getsize=$this->getsizls($category,$subcategory,$brand,'size');           
            $product=$this->kls($category,$subcategory,$brand,$sort,$ratting,$discount,$price,$color,$size,$search);
          
            $pro=array();
            $pricelist=array();
             if($code!="0"){
            $getcode=Coupon::where("code",$code)->first();
            $date=date("Y-m-d");
            $start_date=date("Y-m-d",strtotime($getcode->start_date)); 
            $end_date=date("Y-m-d",strtotime($getcode->end_date));
            if(($date>=$start_date)&&($date<=$end_date)){ 
                $searls=array();             
                if($getcode->coupon_on=='0'){//product
                    foreach ($product as $k) {
                      $products=explode(",",$getcode->product);
                      if(in_array($k->id,$products)){
                            $k->name=substr($k->name,0,15);
                            $getreview=Review::where("product_id",$k->id)->get();
                            $k->total_review=count($getreview);
                            $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                            $k->avgStar=round($avgStar);
                            $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
                            $k->wish=count($wish);
                            $pricelist[]=$k->price;
                            $k->disper=$k->discount;
                            $k->price=$k->selling_price;   
                            $searls[]=$k;
                      }
                    }
                }
                if($getcode->coupon_on=='1'){//category
                   $categorypro=explode(",", $getcode->categories);
                    foreach ($product as $k) {
                        if(in_array($k->category,$categorypro)){
                            $k->name=substr($k->name,0,15);
                            $getreview=Review::where("product_id",$k->id)->get();
                            $k->total_review=count($getreview);
                            $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                            $k->avgStar=round($avgStar);
                            $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
                            $k->wish=count($wish);
                            $pricelist[]=$k->price;
                            $k->disper=$k->discount;
                            $k->price=$k->selling_price; 
                             $searls[]=$k;
                        }
                    }
                }
                $product=$searls;
            }
     }
     else{
         foreach ($product as $k) {
            $k->name=substr($k->name,0,15);
            $getreview=Review::where("product_id",$k->id)->get();
            $k->total_review=count($getreview);
            $avgStar = Review::where("product_id",$k->id)->avg('ratting');
            $k->avgStar=round($avgStar);
            $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
            $k->wish=count($wish);
            $pricelist[]=$k->price;
            $k->disper=$k->discount;
            $k->price=$k->selling_price;   
        }
     }

                $pricels=array();
                if(!empty($pricelist)){
                   $pricels=$this->getpricelist($pricelist);
                }
             $getsublist=$this->subcategoryls($category,$subcategory,$brand);

            $data=array("subcategory"=>$getsublist,"brand"=>$getbrand,"product"=>$product,"price"=>$pricels,"color"=>$getcolorls,"size"=>$getsize);
            return $data;
    }

    public function subcategoryls($category,$subcategory,$brand){
        $subcat=array();
        if($category!="0"){//11
            return $subcat=Categories::where("parent_category",$category)->where("is_delete",'0')->get();
        }       
        elseif($category=="0"&&$subcategory!="0"){//01
           $getpar=Categories::find($subcategory);
           return $subcat=Categories::where("parent_category",$getpar->parent_category)->where("is_delete",'0')->get();
        }
        elseif($category=="0"&&$subcategory=="0"&&$brand!="0"){
           $getbrand=Brand::where("brand_name",$brand)->first();
           if($getbrand){
              $getpar=Categories::find($getbrand->category_id);
             return  $subcat=Categories::where("parent_category",$getpar->parent_category)->where("is_delete",'0')->get();
           }
          
        }
        else{//00
            return $subcat=Categories::where("parent_category","!=","0")->where("is_delete",'0')->get();
        }
        return $subcat;
    }
     public function getcolorls($category,$subcategory,$brand){
     
        $colorls=array();
        $product=array();
          if($category!=0&&$subcategory==0&&$brand==0){//100
         $product=Product::where("category",$category)->where("is_deleted",'0')->select("id","name","category","product_color")->where('status','1')->get();
          }else if($category!=0&&$subcategory==0&&$brand!=0){//101
              $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color")->get();
          }else if($category!=0&&$subcategory!=0&&$brand==0){//110
            $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color")->get();
          }else if($category!=0&&$subcategory!=0&&$brand!=0){//111
              $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("is_deleted",'0')->where("brand",$brand)->where("is_deleted",'0')->select("id","name","category","product_color")->get();
          }
          else if($category==0&&$subcategory==0&&$brand==0){//000
              $product=Product::where("status",'1')->where("is_deleted",'0')->where("is_deleted",'0')->select("id","name","category","product_color")->get();
          }
          else if($category==0&&$subcategory!=0&&$brand==0){//010
            $product=Product::where("subcategory",$subcategory)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color")->get();
          }
          else if($category==0&&$subcategory!=0&&$brand!=0){//011
              $product=Product::where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color")->get();
          }
           else if($category==0&&$subcategory==0&&$brand!=0){//001
              $product=Product::where("brand",$brand)->where("status",'1')->where("is_deleted",'0')->select("id","name","category","product_color")->get();
          }
          

        
    
         foreach ($product as $k) {
              $colorls[]=$k->product_color;
         }
        
       return array_values(array_unique(array_filter($colorls)));
      
   }

       function getsizls($category,$subcategory,$brand,$fields){
     $colorls=array();
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

     public function getbrandlist($category,$subcategory,$brand){
      if($subcategory=="0"&&$brand=="0"){
          $getsubcategory=Categories::where("parent_category",$category)->where("is_active",'1')->where("is_delete",'0')->get();
          $dt=array();
          if(count($getsubcategory)!=0){
              foreach ($getsubcategory as $ke) {
                 $brand=Brand::where("category_id",$ke->id)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
                 foreach ($brand as $b) {
                   $dt[]=$b;
                 }
              }
              return $dt;
          }
      }elseif($subcategory!="0"&&($brand=="0"||$brand!="0")){
           $brand=Brand::where("category_id",$subcategory)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
           return $brand;
      }elseif($subcategory=="0"&&$brand!="0"){
              $getb=Brand::where("brand_name",$brand)->first();
              $brand=Brand::where("category_id",$getb->category_id)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
              return $brand;
      }
   }

     public function kls($category,$subcategory,$brand,$sort,$ratting,$discount,$price,$color,$size,$search){

           if($sort==2){
                $field="price";
                $orderby="ASC";
           }
           elseif($sort==3){
                $field="price";
                $orderby="DESC";
           }
           elseif($sort==4){
                $field="id";
                $orderby="DESC";
           }else{
                $field="id";
                $orderby="ASC";
           }
          
           $product=array();
           $data=array();
              if($category!="0"&&$subcategory=="0"&&$brand=="0"){//100
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                          if($search!="0"){
                              $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                          }
                          else{
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                          }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                          if($search!="0"){
                             $product=Product::where("category",$category)->where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                          }
                          else{
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                          }                          
                      }elseif($color!="0"&&$size=="0"){//10
                              if($search!="0"){
                                 $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                              }
                              else{
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                              }                          
                      }
                      else if($color!="0"&&$size!="0"){//11
                              if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                              }
                              else{
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                              }                           
                      }                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                              }
                              else{
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                              }                           
                          }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }                           
                          }elseif($color!="0"&&$size=="0"){//10
                              if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                              }
                              else{
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                              }                              
                          }else{//11
                              if($search!="0"){
                                 $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                              }
                              else{
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                              }                           
                          }
                         
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                              if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                              }
                              else{
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                              }                               
                           }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }                              
                           }elseif($color!="0"&&$size=="0"){//10
                                if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                }
                               
                           }else{//11
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }                              
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  if($search!="0"){
                                    $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                  }
                                  else{
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                  }                                  
                               }elseif($color=="0"&&$size!="0"){//01
                                    if($search!="0"){
                                      $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                    }
                                    
                                }elseif($color!="0"&&$size=="0"){//10
                                    if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                    }
                                   
                                }else{//11
                                      if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                    
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }
                        }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }                             
                        }elseif($color!="0"&&$size=="0"){//10
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                }                             
                        }else{//11
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }                              
                        }                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }                            
                         }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }                              
                          }elseif($color!="0"&&$size=="0"){//10
                                  if($search!="0"){
                                    $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                  }
                                  else{
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                  }
                                 
                          }else{//11
                                   if($search!="0"){
                                       $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                    }
                              
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                                  if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                  }
                                  else{
                                     $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                  }                               
                           }elseif($color=="0"&&$size!="0"){//01
                                  if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                  }
                                  else{
                                     $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                  }                                 
                            }elseif($color!="0"&&$size=="0"){//10
                                      if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                            }else{//11
                                      if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }                                 
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                      if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                  
                               }elseif($color=="0"&&$size!="0"){//01
                                       if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                       }
                                       else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                       }                                    
                               }elseif($color!="0"&&$size=="0"){//10
                                      if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                    
                               }else{//11
                                     if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }                                  
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                    $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                }
                                else{
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                }
                         
                      }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                          
                      }elseif($color!="0"&&$size=="0"){//10
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                }                          
                      }else{//11
                                if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                           
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                }
                             
                          }elseif($color=="0"&&$size!="0"){//01
                                  if($search!="0"){
                                      $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                  }
                                  else{
                                      $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                  }                            
                          }elseif($color!="0"&&$size=="0"){//10
                                    if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                                    else{
                                      $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                                
                          }else{//11
                                      if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                             
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                                   if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                    }
                                
                             }elseif($color=="0"&&$size!="0"){//01
                                    if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                    }
                                    else{
                                      $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                    }
                                 
                             }elseif($color!="0"&&$size=="0"){//10
                                    if($search!="0"){
                                      $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                                    else{
                                      $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                                   
                             }else{//11
                                      if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }                                   
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                    if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                    }
                                    else{
                                      $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                    }                                   
                              }elseif($color=="0"&&$size!="0"){//01
                                    if($search!="0"){
                                       $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                    }                                  
                              }elseif($color!="0"&&$size=="0"){//10
                                    if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                                    else{
                                      $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                                   
                              }else{//11
                                     if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                    }
                                  
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }                            
                         }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                            
                         }elseif($color!="0"&&$size=="0"){//10
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                }
                              
                         }else{//11
                                 if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                            
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }                              
                          }elseif($color!="0"&&$size=="0"){//10
                                if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                }
                                 
                          }else{//11
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }                                
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                }
                               
                            }elseif($color=="0"&&$size!="0"){//01
                                  if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                  }
                                  else{
                                     $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                  }
                               
                            }elseif($color!="0"&&$size=="0"){//10
                                    if($search!="0"){
                                       $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                    }
                               
                            }else{//11
                                    if($search!="0"){
                                      $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                    }
                                    else{
                                      $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                    }                                
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                                if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                }
                               
                          }else{//11
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                              
                          }
                          
                      }
                 }
              }
              if($category!="0"&&$subcategory=="0"&&$brand!="0"){//101   
                if($discount=="0"&&$ratting=="0"&&$price=="0"){ //000
                      if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                    $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                }
                           
                      }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                        
                          
                      }elseif($color!="0"&&$size=="0"){//10
                                if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                }
                          
                      }
                      else if($color!="0"&&$size!="0"){//11
                               if($search!="0"){
                                    $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                           
                      }
                    
                }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                    $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                }
                            
                          }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                            
                          }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                }
                             
                          }else{//11
                               if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                }                           
                          }
                         
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                                if($search!="0"){
                                  $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                }
                                else{
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                   $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                }
                              
                           }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                     $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                }
                                else{
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                }
                              
                           }else{//11
                                  if($search!="0"){
                                       $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                  }
                                  else{
                                     $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                  }
                              
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                    if($search!="0"){
                                       $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                    }
                                    else{
                                       $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                    }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                      if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                                }elseif($color!="0"&&$size=="0"){//10
                                     if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                   
                                }else{//11
                                       if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                    
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                                  if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                              
                        }elseif($color=="0"&&$size!="0"){//01
                                     if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                        }elseif($color!="0"&&$size=="0"){//10
                                    if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                            
                        }else{//11
                                 if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                             
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                                    if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                                  if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                                 if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                 
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                            }else{//11
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                               }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                    
                               }else{//11
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                          
                      }else{//11
                         if($search!="0"){
                                              $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                 
                             }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                
                             }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                   
                             }else{//11
                               if($search!="0"){
                                          $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where('name', 'LIKE', '%' . $search . '%')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                   
                              }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                              }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                   
                              }else{//11
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                                  if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                              if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                             
                         }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                               
                         }else{//11
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                             
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                 
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                
                            }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                            }else{//11
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                          }
                          
                      }
                 }
              }
               
              if($category!="0"&&$subcategory!="0"&&$brand=="0"){//110

                 if($discount=="0"&&$ratting=="0"&&$price=="0"){
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                                      else{
                                          $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                         
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                        
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                          
                      }
                      else if($color!="0"&&$size!="0"){//11
                         if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                          
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                            
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                              
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                            
                          }
                         
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                           }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                               
                           }else{//11
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                  
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                                }elseif($color!="0"&&$size=="0"){//10
                                   if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                   
                                }else{//11
                                   if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010

                        if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }

                             
                             
                        }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                        }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                              
                        }else{//11
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                            }else{//11
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                  
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                  
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                               }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                    
                               }else{//11
                                 if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                           
                      }else{//11
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                             
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                 
                             }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                
                             }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                   
                             }else{//11
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                  
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                   
                              }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                              }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                  
                              }else{//11
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                         }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                              
                         }else{//11
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                             
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                 
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                
                            }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                            }else{//11
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                          }
                          
                      }
                
                 }
              }
              
              if($category!="0"&&$subcategory!="0"&&$brand!="0"){//111
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                           
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                         
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                           
                      }
                      else if($color!="0"&&$size!="0"){//11
                         if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                          
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                            
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                              
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                          }
                         
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                           }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                               
                           }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                  
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                                }elseif($color!="0"&&$size=="0"){//10
                                   if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                    
                                }else{//11
                                   if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                    
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                        }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                        }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                              
                        }else{//11
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                  
                            }else{//11
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                               }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                               }else{//11
                                 if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                           
                      }else{//11
                         if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                          $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      } 
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                             
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                 
                             }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                 
                             }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                   
                             }else{//11
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                   
                              }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                              }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                   
                              }else{//11
                                 if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                         }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                               
                         }else{//11
                           if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                            
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                            }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                            }else{//11
                               if($search!="0"){
                                           $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                              
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("category",$category)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                          
                      }
                 }
              }
              if($category=="0"&&$subcategory=="0"&&$brand=="0"){//000
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                        
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                 $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                          
                      }
                      else if($color!="0"&&$size!="0"){//11
                         if($search!="0"){
                                $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                           
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                              
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                          }
                         
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                              
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                           }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                              
                           }else{//11
                             if($search!="0"){
                                      $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                                }elseif($color!="0"&&$size=="0"){//10
                                   if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                    
                                }else{//11
                                   if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                    
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                        }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                        }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                             
                        }else{//11
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                 
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                            }else{//11
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                  
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                  
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                               }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                    
                               }else{//11
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                  
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                           
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                           
                      }else{//11
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                          
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                
                             }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                
                             }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                   
                             }else{//11
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                   
                              }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                  
                              }else{//11
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                             
                         }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                          $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                             
                         }else{//11
                           if($search!="0"){
                                         $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                            
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                            }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                            }else{//11
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                          }
                          
                      }
                 }
              }              
              if($category=="0"&&$subcategory=="0"&&$brand!="0"){//001
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                           
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                         
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                          
                      }
                      else if($color!="0"&&$size!="0"){//11
                         if($search!="0"){
                                           $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                          
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                            
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                             $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                                      else{
                                           $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                           
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                          }
                         
                      }elseif($str[1]=="Above"){

                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                            $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                                      else{
                                          $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                             
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                           }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                               
                           }else{//11
                             if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                            $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                          $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                                }elseif($color!="0"&&$size=="0"){//10
                                   if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                    
                                }else{//11
                                   if($search!="0"){
                                           $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      } 
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010

                        if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                        }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                        }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                              
                        }else{//11
                           if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("brand",$brand)->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      } 
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                            }else{//11
                               if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                  
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                  
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                               }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                               }else{//11
                                 if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                  
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                           
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                          
                      }else{//11
                         if($search!="0"){
                                      $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                             
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                 
                             }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                
                             }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                           $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                           $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                             }else{//11
                               if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                  
                              }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                              }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                   
                              }else{//11
                                 if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                   
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                          $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                             
                         }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                               
                         }else{//11
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                             
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                           $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                 
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                            }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                            }else{//11
                               if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                             
                               
                          }else{//11
                              
                                if($search!="0"){
                                   $product=Product::where("brand",$brand)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();

                                      }
                                      else{
                                         $product=Product::where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                          }
                          
                      }
                 }
              } 
              if($category=="0"&&$subcategory!="0"&&$brand=="0"){//010
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                       $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                        
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                           $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                                      else{
                                           $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                        
                      }
                      else if($color!="0"&&$size!="0"){//11
                         if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                           
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                              
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      } 
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                          }
                         
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                           }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                               
                           }else{//11
                             if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                                }elseif($color!="0"&&$size=="0"){//10
                                   if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                    
                                }else{//11
                                   if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                    
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                              
                        }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                        }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                             
                        }else{//11
                           if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                 
                          }else{//11
                             if($search!="0"){
                                          $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                            }else{//11
                               if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                               }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                          $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                    
                               }else{//11
                                 if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                          
                      }else{//11
                         if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                          $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                             
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                           $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                             
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                 
                             }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                      $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                 
                             }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                  
                             }else{//11
                               if($search!="0"){
                                       $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                  
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                  
                              }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                          $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                              }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                  
                              }else{//11
                                 if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                  
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                             
                         }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                         $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                              
                         }else{//11
                           if($search!="0"){
                                         $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                            
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                            }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                         $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                            }else{//11
                               if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("subcategory",$subcategory)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                          }
                          
                      }
                 }
              }
              if($category=="0"&&$subcategory!="0"&&$brand!="0"){//011
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->get();
                                      }
                          
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                        
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                           $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                                      else{
                                           $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("product_color",$color)->get();
                                      }
                        
                      }
                      else if($color!="0"&&$size!="0"){//11
                         if($search!="0"){
                                    $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                           
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                    $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->get();
                                      }
                            
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                     $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->get();
                                      }
                             
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                           
                          }
                         
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->get();
                                      }
                               
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                           }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->get();
                                      }
                               
                           }else{//11
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                                }elseif($color!="0"&&$size=="0"){//10
                                   if($search!="0"){
                                           $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->get();
                                      }
                                   
                                }else{//11
                                   if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                    
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                           if($search!="0"){  
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                        }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                             
                        }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                              
                        }else{//11
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                           $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                            
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                          }
                        
                      }elseif($str[1]=="Above"){
                           if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                
                           }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                 
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      } 
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                   
                            }else{//11
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                 
                               }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                    
                               }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){
                                            $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                  
                               }else{//11
                                 if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                         if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                          $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->get();
                                      }
                         
                      }elseif($color=="0"&&$size!="0"){//01
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                          
                      }elseif($color!="0"&&$size=="0"){//10
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                          
                      }else{//11
                         if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                          
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                          $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->get();
                                      }
                            
                          }elseif($color=="0"&&$size!="0"){//01
                             if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                              
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                               
                          }else{//11
                             if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                            
                          }
                         
                      }elseif($str[1]=="Above"){
                             if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->get();
                                      }
                                
                             }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                 
                             }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                  
                             }else{//11
                                   if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->get();
                                      }
                                   
                              }elseif($color=="0"&&$size!="0"){//01
                                 if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                   
                              }elseif($color!="0"&&$size=="0"){//10
                                 if($search!="0"){  
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                  
                              }else{//11
                                 if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                          $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                 
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                         $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                            
                         }elseif($color!="0"&&$size=="0"){//10
                           if($search!="0"){
                                        $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                               
                         }else{//11
                           if($search!="0"){
                                       $product=Product::where("discount","<=",$discount)->where('name', 'LIKE', '%' . $search . '%')->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                            
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                           if($search!="0"){
                                          $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->get();
                                      }
                             
                         }elseif($color=="0"&&$size!="0"){//01
                           if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->get();
                                      }
                                
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                
                          }
                        
                      }elseif($str[1]=="Above"){
                            if($color=="0"&&$size=="0"){//00
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                               
                            }elseif($color=="0"&&$size!="0"){//01
                               if($search!="0"){
                                       $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                            }elseif($color!="0"&&$size=="0"){//10
                               if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                
                            }else{//11
                               if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                               
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->get();
                                      }
                              
                          }elseif($color=="0"&&$size!="0"){//01
                                if($search!="0"){
                                        $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                                      else{
                                        $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->get();
                                      }
                               
                          }elseif($color!="0"&&$size=="0"){//10 if($search!="0"){
                                if($search!="0"){
                                           $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->get();
                                      }
                              
                          }else{//11
                             if($search!="0"){
                                         $product=Product::where("status",'1')->where('name', 'LIKE', '%' . $search . '%')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();

                                      }
                                      else{
                                         $product=Product::where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color","category")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->get();
                                      }
                              
                          }
                          
                      }
                 }
              }   
              
              
              return $product;
  }
}
