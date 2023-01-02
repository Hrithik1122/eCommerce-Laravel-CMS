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
class productfilterController extends Controller {
    
  public function productfilter(Request $request){
  //dd($request->all());exit;
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
                      'size'=>'required'        
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
                    'size.required'=>'size is required'
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
                $getbrand=$this->getbrandlist($category,$subcategory,$brand);
                $getsub=$this->getsublist($category,$subcategory,$brand);
                $getsize=$this->getsizls($category,$subcategory,$brand,'size');

                $getcolorls=$this->getcolorls($category,$subcategory,$brand,'color');
               
                if(count($getsub)!=0){
                    $category=$getsub[0]->parent_category;
                }
                
                $klist=$this->kls($category,$subcategory,$brand,$filter,$ratting,$discount,$price,$color,$size);
 
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
                         
                         unset($k->selling_price);
                }

                  $price=$this->getpricelist($category,$subcategory,$brand);
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

   public function getbrandlist($category,$subcategory,$brand){
      if($subcategory==0&&$brand==0){
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
      }elseif($subcategory!=0&&($brand==0||$brand!=0)){
           $brand=Brand::where("category_id",$subcategory)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
           return $brand;
      }elseif($subcategory==0&&$brand!=0){
              $getb=Brand::find($brand);
              $brand=Brand::where("category_id",$getb->category_id)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
              return $brand;
      }
   }

   public function getsublist($category,$subcategory,$brand){
        if($category==0&&$subcategory==0&&$brand!=0){//001
            $brd=Brand::find($brand);
            $getsub=Categories::find($brd->category_id);
            if($getsub){
               $category=Categories::where("parent_category",$getsub->parent_category)->select("id","name","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
               return $category;
            }
        }elseif($category==0&&$subcategory!=0&&$brand==0){//010
            $getsub=Categories::find($subcategory);
            if($getsub){
               $category=Categories::where("parent_category",$getsub->parent_category)->select("id","name","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
               return $category;
            }
        }elseif($category==0&&$subcategory!=0&&$brand!=0){//011
            $getsub=Categories::find($subcategory);
            if($getsub){
               $category=Categories::where("parent_category",$getsub->parent_category)->select("id","name","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
               return $category;
            }
        }elseif($category!=0){//100
           $category=Categories::where("parent_category",$category)->select("id","name","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
           return $category;
        }
       
   }
 


  public function kls($category,$subcategory,$brand,$sort,$ratting,$discount,$price,$color,$size){

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
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10

                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);

                             

                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                 }
              }
              if($category!="0"&&$subcategory=="0"&&$brand!="0"){//101
                 

                if($discount=="0"&&$ratting=="0"&&$price=="0"){ //000
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                 }
              }
               
              if($category!="0"&&$subcategory!="0"&&$brand=="0"){//110

                 if($discount=="0"&&$ratting=="0"&&$price=="0"){
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010

                        if($color=="0"&&$size=="0"){//00

                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                             
                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                
                 }
              }
              
              if($category!="0"&&$subcategory!="0"&&$brand!="0"){//111
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","name","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                 }
            }  
              
              
              return $product;
  }
   public function getpricelist($category,$subcategory,$brand){
    $product=array();
       if($category!=0&&$subcategory==0&&$brand==0){
         $product=Product::where("category",$category)->where("is_deleted",'0')->get();
      }else if($category!=0&&$subcategory==0&&$brand!=0){
          $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->get();
      }else if($category!=0&&$subcategory!=0&&$brand==0){
        $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->get();
      }else if($category!=0&&$subcategory!=0&&$brand!=0){
          $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->get();
      }
       foreach ($product as $k) {
                    $pricelist[]=$k->selling_price;
                  }
                     $pricels=array();
          if(!empty($pricelist)){
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
          }
      return $pricels;
   }
  
   public function getcolorls($category,$subcategory,$brand){
       // $color=$request->get("color");
    $product=array();
        $colorls=array();
          if($category!=0&&$subcategory==0&&$brand==0){
         $product=Product::where("category",$category)->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
      }else if($category!=0&&$subcategory==0&&$brand!=0){
          $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->select("id","name","category","product_color","color_name")->get();
      }else if($category!=0&&$subcategory!=0&&$brand==0){
        $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->select("id","name","category","product_color","color_name")->get();
      }else if($category!=0&&$subcategory!=0&&$brand!=0){
          $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->select("id","name","category","product_color","color_name")->get();
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
          if($category!=0&&$subcategory==0&&$brand==0){
         $product=Product::with('optionls')->where("category",$category)->where("is_deleted",'0')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory==0&&$brand!=0){
          $product=Product::with('optionls')->where("category",$category)->where("brand",$brand)->where("status",'1')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory!=0&&$brand==0){
        $product=Product::with('optionls')->where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory!=0&&$brand!=0){
          $product=Product::with('optionls')->where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->select("id","name","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
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

