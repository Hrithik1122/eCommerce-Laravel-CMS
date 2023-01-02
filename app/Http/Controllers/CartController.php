<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Model\Categories;
use App\Model\Brand;
use App\Model\Offer;
use App\Model\Product;
use App\Model\Wishlist;
use App\Model\Shipping;
use App\Model\Setting;
use App\Model\Taxes;
use App\Model\ProductOption;
use App\Model\Cartdata;
use Image;
use Hash;
use Cart;
use Auth;
class CartController extends Controller {
   public function __construct() {
         parent::callschedule();
         $setting=Setting::find(1);
         $res_curr=explode("-",$setting->default_currency);
         $shiping=Shipping::find(1);
         
        
        if($shiping){
          Session::put("home_delivery",$shiping->id."#".$shiping->cost);
        }
        else{
          Session::put("home_delivery","0#0");
        }
    }
   public function addtocart(Request $request){
       $setting=Setting::find(1);
       $res_curr=explode("-",$setting->default_currency);
       $getproduct=Product::find($request->get("product_id"));
       $getoption=ProductOption::where("product_id",$request->get("product_id"))->first();
       $label=array();
       $priceda=array();
       if($getoption){
           $label[]=explode("#",$getoption->label);
           $priceda[]=explode("#",$getoption->price);
       }
      
       $lb="";
       $pr="";
       $i=0;
       $j=0;
       $price="";
      
       if($request->get("optionarr")){
            foreach($label as $ab){
              foreach($ab as $a){
                  if($i==0){
                       $lb=$lb.$a;
                  }else{
                       $lb=$lb.','.$a;
                  }
                 $i++;
              }
           }
           foreach($priceda as $ab){
              foreach($ab as $a){
                  if($j==0){
                       $pr=$pr.$a;
                  }else{
                       $pr=$pr.','.$a;
                  }
                 $j++;
              }
           }
           $lb=explode(",",$lb);
           $pr=explode(",",$pr);
           $lb1=explode(",",$request->get("labelarr"));
           $pr1=explode(",",$request->get("price_sel"));
           for($i=0;$i<count($lb1);$i++){
               if(array_search($lb1[$i],$lb)){
                   $pr1[$i]=$pr[array_search($lb1[$i],$lb)];
                   
               }
           }
           $price=implode(",",$pr1);
           
       }
      
      
       $main_array=array(
          "option"=>$request->get("optionarr"),
          "label"=>$request->get("labelarr"),
          "price"=>$price
       );
       
       
       $price=(float)$getproduct->selling_price+(float)array_sum(explode(",",$price));
       Cart::add(mt_rand(100000,999999),$request->get("product_name"),number_format((float)$price, 2, '.', ''),$request->get("qty"), array($main_array));
       
       Session::flash('message',__('messages_error_success.product_add_success')); 
       Session::flash('alert-class', 'alert-success');
       $cartCollection = Cart::getContent();
       $i=0;
       $txt="";
       $productdata=Product::all();
       if($cartCollection->count()!=0){
           $txt=$txt.'<div class="cart-pop_up-sbox">';
            foreach($cartCollection as $item){
                $txt=$txt.'<div class="cart-pop_up-content"><div class="cart-pop_up-imgbox">';
                    foreach($productdata as $hs){
                        if($hs->name==$item->name){
                            $txt=$txt.'<img src="'.asset('public/upload/product').'/'.$hs->basic_image.'" width="75" height="auto">';
                             break;
                        }
                    }
                $txt=$txt.'</div><div class="cart-pop_up-detail-box"><div class="pop_up-detail-head"><h2>'.$item->name.'</h2><div class="cart-pop_up-cross"><i class="fa fa-times" aria-hidden="true"></i><span>'.$item->quantity.'</span></div><a href="javascript:deletecartitem('.$item->id.')"><i class="fa fa-trash-o pop_up-delete" aria-hidden="true" style="color:'.__('messages.site_color').' !important"></i></a></div><p>';
                $option=explode(",",$item->attributes[0]['option']);
                $label=explode(",",$item->attributes[0]['label']);
                
                   for($i=0;$i<count($option);$i++){
                                $txt=$txt.' <span style="font-size:small"><b>'.$option[$i].'</b><span>  '.$label[$i].'</span></span></br>';
                   }
                    $txt=$txt.'</p><h2>'.$res_curr[1].number_format((float)$item->price, 2, '.', '').'</h2></div></div>';
            }
            $txt=$txt.'<div class="cart-pop_up-subtotal"><h3>'.__('messages.subtotal').' :</h3><h3 class="cart-pop_up-prize">'.$res_curr[1].number_format(Cart::getTotal(), 2, '.', '').'</h3></div><a href="'.url('cartdetail').'"><div class="pop_up-viewcart-b">'.__('messages.view_cart').'</div></a>';
            $txt=$txt.'<a href="#"><div class=""><form action="'.url('checkout').'" method="post">'.csrf_field().'<input type="hidden" name="check_delivery" id="check_delivery" value="'.Session::get('home_delivery').'" /><input type="hidden" name="check_discount_type" id="check_discount_type" value="1" /><input type="hidden" name="check_discount_value" id="check_discount_value"   value="15"/><input type="hidden" name="check_free_shipping" id="check_free_shipping" value="0" /><input type="hidden" name="check_coupon_value" id="check_coupon_value" /><input type="hidden" name="check_couponcode" id="check_couponcode" /><button value="submit" class="pop_up-checkout-b" type="submit" onclick="Checkout()" style="background-color: '.__('messages.site_color').'!important">'.__('messages.proceed_to_checkout').'</button></form></div></a>';
            $txt=$txt.'</div>';
       }
       else{
           $txt='<div class="cart-pop_up-content"><div class="cart-pop_up-content empty"><div class="cart-pop_up-imgbox"><img src="'.asset('public/Ecommerce/images/empty.png').'"><h1>'.__('messages.cart_empty').'</h1></div></div></div>';
       }
       $data=array("content"=>$txt,"totalcart"=>$cartCollection->count());
       return json_encode($data);
   }

   public function addtowish(Request $request){
    $setting=Setting::find(1);
       $res_curr=explode("-",$setting->default_currency);
        $main_array=array(
          "option"=>"",
          "label"=>"",
          "price"=>""
       );
              $getproduct=Product::find($request->get("product_id"));
       
       Cart::add(mt_rand(100000,999999),$request->get("product_name"),$getproduct->selling_price,$request->get("qty"), array($main_array));
       $wish=Wishlist::where("product_id",$request->get("product_id"))->where("user_id",Auth::id())->first();
       if($wish){
          $wish->delete();
       }
       $getwish=Wishlist::with('productdata')->where("user_id",Auth::id())->get();
     
       $txt='<tr class="pro-heading" style="background:'.__('messages.site_color').' !important"><th>'.__("messages.del").'</th><th>'.__("messages.images").'</th><th>'.__("messages.product").'</th><th>'.__("messages.stock_status").'</th><th>'.__("messages.price").'</th><th></th></tr>';
       if(count($getwish)!=0){
           foreach($getwish as $mw){
                   $txt=$txt.'<tr><td class="Delete-icon"><a href="javascript:;" onclick="deletewish('.$mw->product_id.')"><i class="fa fa-trash-o" aria-hidden="true"></i></a><span>'.__('messages.del').':</span></td><td class="cart-img"><img src='.asset('public/upload/product').'/'.$mw->productdata->basic_image.'><span>'.__('messages.images').' :</span></td><td class="place-text"><div class="text-a"><span>'.__('messages.product').' :</span><h1>'.$mw->productdata->name.'</h1></div></td><td class="Stock-text">';
                   if($mw->productdata->stock=='0'){
                       $txt=$txt.__("messages.outstock");
                   }
                   else{
                       $txt=$txt.__("messages.in_stock");
                   }
                   $txt=$txt.'<span>'.__('messages.stock_status').':</span></td><td class="price">'.$res_curr[1].$mw->productdata->price.'<span>'.__('messages.price').':</span></td><td class="add"><a onclick="addwishtocart('.$mw->product_id.',' . "'" . $mw->productdata->name. "'" . ',1,'.$mw->productdata->price.')" style="border-color:'.__('messages.site_color').'!important">'.__('messages.add_to_cart').'</a></td></tr>';
           }
       }
       else{
           $txt=$txt.'<tr><td colspan="6" class="emptywish">'.__('messages.Your wishlist is currently empty!').'</td></tr>';
       }
        $cartCollection = Cart::getContent();
       $i=0;
       $txtcart="";
       $productdata=Product::all();
       if($cartCollection->count()!=0){
           $txtcart=$txtcart.'<div class="cart-pop_up-sbox">';
            foreach($cartCollection as $item){
                $txtcart=$txtcart.'<div class="cart-pop_up-content"><div class="cart-pop_up-imgbox">';
                    foreach($productdata as $hs){
                       if($hs->name==$item->name){
                            $txtcart=$txtcart.'<img src="'.asset('public/upload/product').'/'.$hs->basic_image.'" width="75" height="auto">';
                             break;
                        }
                    }
                $txtcart=$txtcart.'</div><div class="cart-pop_up-detail-box"><div class="pop_up-detail-head"><h2>'.$item->name.'</h2><div class="cart-pop_up-cross"><i class="fa fa-times" aria-hidden="true"></i><span>'.$item->quantity.'</span></div><a href="javascript:deletecartitem('.$item->id.')"><i class="fa fa-trash-o pop_up-delete" aria-hidden="true" style="color:'.__('messages.site_color').' !important"></i></a></div><p>';
                $option=explode(",",$item->attributes[0]['option']);
                $label=explode(",",$item->attributes[0]['label']);
                
                   for($i=0;$i<count($option);$i++){
                                $txtcart=$txtcart.' <span style="font-size:small"><b>'.$option[$i].'</b><span>  '.$label[$i].'</span></span></br>';
                                }
                    $txtcart=$txtcart.'</p><h2>'.$res_curr[1].number_format((float)$item->price, 2, '.', '').'</h2></div></div>';
            }
            $txtcart=$txtcart.'<div class="cart-pop_up-subtotal"><h3>'.__('messages.subtotal').' :</h3><h3 class="cart-pop_up-prize">'.$res_curr[1].number_format(Cart::getTotal(), 2, '.', '').'</h3></div><a href="'.url('cartdetail').'"><div class="pop_up-viewcart-b">'.__('messages.view_cart').'</div></a>';
            $txtcart=$txtcart.'<a href="#"><div class=""><form action="'.url('checkout').'" method="post">'.csrf_field().'<input type="hidden" name="check_delivery" id="check_delivery" value="'.Session::get('home_delivery').'" /><input type="hidden" name="check_discount_type" id="check_discount_type" value="1" /><input type="hidden" name="check_discount_value" id="check_discount_value"   value="15"/><input type="hidden" name="check_free_shipping" id="check_free_shipping" value="0" /><input type="hidden" name="check_coupon_value" id="check_coupon_value" /><input type="hidden" name="check_couponcode" id="check_couponcode" /><button value="submit" class="pop_up-checkout-b" type="submit" onclick="Checkout()" style="background-color: '.__('messages.site_color').'!important">'.__('messages.proceed_to_checkout').'</button></form></div></a>';
            $txtcart=$txtcart.'</div>';
       }
       else{
           $txtcart='<div class="cart-pop_up-content"><div class="cart-pop_up-content empty"><div class="cart-pop_up-imgbox"><img src="'.asset('public/Ecommerce/images/empty.png').'"><h1>'.__('messages.cart_empty').'</h1></div></div></div>';
       }
       $data=array("content"=>$txt,"total"=>count($getwish),"totalcart"=>$cartCollection->count(),"cartcontent"=>$txtcart);
       Session::flash('message',__('messages_error_success.product_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return json_encode($data);
   }

   public function updatecartqty(Request $request){
        $setting=Setting::find(1);
       $res_curr=explode("-",$setting->default_currency);
        if($request->get("qty")==1){//add qty
           Cart::update($request->get('item_id'), array('quantity' =>1));
        }
        if($request->get("qty")==0){//minus qty
          Cart::update($request->get('item_id'), array('quantity' => -1));
        }
        $cartCollection = Cart::getContent();
                      $totalamountarr=array();
                     foreach ($cartCollection as $car) {
                       $totalamount="";
                       $totalamount=(float)$car->quantity*(float)$car->price;
                       $totalamountarr[]=round($totalamount,2);
        }
        $subtotal=Session::get("usercurrency").array_sum($totalamountarr);
        $finalresult=(float)array_sum($totalamountarr);
        $data=array("subtotal"=>$subtotal,"finaltotal"=>$finalresult);
        return $data;
   }


   public function getcartview(){
    
      $cartCollection = Cart::getContent();
      echo "<pre>";
      print_r($cartCollection);exit;
      $main_array=array();  
        foreach ($cartCollection as $item) {
           $order=array();
           $gettotal=array();
           $subtotal=$item->price*$item->quantity;
           $producttax=Product::find($item->name);
           $taxdata=Taxes::find($producttax->tax_class);
           $a=$taxdata->rate/100;
           $b=$subtotal*$a;
           $order["ProductId"]=$item->id;
           $order["ProductQty"]=$item->quantity;
           $order["ProductAmt"]=$item->price;
           $order["ProductTotal"]=$item->price*$item->quantity;
           $order["tax_name"]=$taxdata->tax_name;
           $order["tax_amount"]=number_format((float)$b, 2, '.', '');;
           $order["exterdata"]=$item->attributes[0];
           $main_array[]=$order;
        }
     return json_encode(array("order"=>$main_array));
   }

   public function cartdetail(){
        $shipping=Shipping::all();
        $setting=Setting::find(1);
       $res_curr=explode("-",$setting->default_currency);
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist();  
         $mywish=Wishlist::where("user_id",Auth::id())->get();  
        return view("user.product.mycart")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("shipping",$shipping)->with("setting",$setting);
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
  
    public function deletecartitem($id){
       Cart::remove($id);
       $cartCollection = Cart::getContent();
       $setting=Setting::find(1);
       $res_curr=explode("-",$setting->default_currency);
       $i=0;
       $txt="";
       $productdata=Product::all();
       if($cartCollection->count()!=0){
           $txt=$txt.'<div class="cart-pop_up-sbox">';
            foreach($cartCollection as $item){
                $txt=$txt.'<div class="cart-pop_up-content"><div class="cart-pop_up-imgbox">';
                    foreach($productdata as $hs){
                         if($hs->name==$item->name){
                            $txt=$txt.'<img src="'.asset('public/upload/product').'/'.$hs->basic_image.'" width="75" height="auto">';
                             break;
                        }
                    }
                $txt=$txt.'</div><div class="cart-pop_up-detail-box"><div class="pop_up-detail-head"><h2>'.$item->name.'</h2><div class="cart-pop_up-cross"><i class="fa fa-times" aria-hidden="true"></i><span>'.$item->quantity.'</span></div><a href="javascript:deletecartitem('.$item->id.')"><i class="fa fa-trash-o pop_up-delete" aria-hidden="true" style="color:'.__('messages.site_color').' !important"></i></a></div><p>';
                $option=explode(",",$item->attributes[0]['option']);
                $label=explode(",",$item->attributes[0]['label']);
                 for($i=0;$i<count($option);$i++){
                                $txt=$txt.' <span style="font-size:small"><b>'.$option[$i].'</b><span>  '.$label[$i].'</span></span></br>';
                   }
                    $txt=$txt.'</p><h2>'.$res_curr[1].number_format((float)$item->price, 2, '.', '').'</h2></div></div>';
            }
            $txt=$txt.'<div class="cart-pop_up-subtotal"><h3>'.__('messages.subtotal').' :</h3><h3 class="cart-pop_up-prize">'.$res_curr[1].Cart::getTotal().'</h3></div><a href="'.url('cartdetail').'"><div class="pop_up-viewcart-b">'.__('messages.view_cart').'</div></a>';
            $txt=$txt.'<a href="#"><div class=""><form action="'.url('checkout').'" method="post">'.csrf_field().'<input type="hidden" name="check_delivery" id="check_delivery" value="'.Session::get('home_delivery').'" /><input type="hidden" name="check_discount_type" id="check_discount_type" value="1" /><input type="hidden" name="check_discount_value" id="check_discount_value"   value="15"/><input type="hidden" name="check_free_shipping" id="check_free_shipping" value="0" /><input type="hidden" name="check_coupon_value" id="check_coupon_value" /><input type="hidden" name="check_couponcode" id="check_couponcode" /><button value="submit" class="pop_up-checkout-b" type="submit" onclick="Checkout()" style="background-color: '.__('messages.site_color').'!important">'.__('messages.proceed_to_checkout').'</button></form></div></a>';
            $txt=$txt.'</div>';
       }
       else{
           $txt='<div class="cart-pop_up-content"><div class="cart-pop_up-content empty"><div class="cart-pop_up-imgbox"><img src="'.asset('public/Ecommerce/images/empty.png').'"><h1>'.__('messages.cart_empty').'</h1></div></div></div>';
       }
       $showcount=0;
       $i=0;
       $mycarttotal='<tr class="pro-heading" style="background:'.__('messages.site_color').'!important"><th>'.__('messages.del').'</th><th>'.__('messages.images').'</th><th>'.__('messages.product').'</th><th>'.__('messages.price').'</th><th>'.__('messages.Quanitity').'</th><th>'.__('messages.total').'</th></tr>';
       if($cartCollection->count()!=0){
           foreach($cartCollection as $item){
               $mycarttotal=$mycarttotal.'<tr><td class="Delete-icon"><a href="javascript:deletecartitem('.$item->id.')"><i class="fa fa-trash-o" aria-hidden="true"></i><span>'.__('messages.del').' :</span></a></td><td class="cart-img">';
               foreach($productdata as $hs){
                   if($hs->name==$item->name){
                       $mycarttotal=$mycarttotal.'<img src="'.asset('public/upload/product').'/'.$hs->basic_image.'" width="75" height="auto">';
                       break;
                   }
               }
               $total=$item->price*$item->quantity;
               $mycarttotal=$mycarttotal.'<span>'.__('messages.images').':</span></td><td class="cartpro-text"><span>'.__('messages.product').':</span>'.$item->name.'</td><td class="price"><p>'.$res_curr[1].'</p><circle id="pricecart'.$i.'">'.number_format((float)$item->price, 2, '.', '').'</circle><span>'.__('messages.price').':</span></td><td class="Quanitity"><div class="qut-box"><button><i class="minus" aria-hidden="true" onclick="usqty('.$i.',0,'.$item->id.')">-</i></button><input type="text" name="text" id="quantity'.$i.'" placeholder="1" value="'.$item->quantity.'"><button><i class="plus" aria-hidden="true" onclick="usqty('.$i.',1,'.$item->id.')">+</i></button></div><span>'.__('messages.Quanitity').':</span></td><td class="total">'.$res_curr[1].'<circle id="totalprice'.$i.'">'.number_format((float)$total, 2, '.', '').'</circle><span>'.__('messages.total').':</span></td></tr>';
               $i++;
           }
          $showcount=1;
       }
       else{
           $mycarttotal=$mycarttotal.'<tr><td colspan="6" class="emptywish">'.__('messages.cart_empty').'</td></tr>';
       }
       $data=array("content"=>$txt,"totalcart"=>$cartCollection->count(),"cartsubtotal"=>Cart::getTotal(),"mycart"=>$mycarttotal,"displaycart"=>$showcount);
       return json_encode($data);
    }


}