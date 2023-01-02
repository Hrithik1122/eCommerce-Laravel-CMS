<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use Auth;
use DataTables;
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
use App\Model\Coupon;
use App\User;
use App\Model\Pages;
use App\Model\Shipping;
use App\Model\CartData;
use Mail;
use Cart;
use Config;
use App\Model\Newsletter;
use Hash;
use DB;
use Artisan;
class FrontController extends Controller {
    public function __construct() {
         parent::callschedule();
         $shiping=Shipping::find(1);
        
        if($shiping){
          Session::put("home_delivery",$shiping->id."#".$shiping->cost);
        }
        else{
          Session::put("home_delivery","0#0");
        }
    }

    public function adddefaultreview(){
       $Product=Product::all();
       foreach($Product as $k){
           $k->MRP=number_format((float)$k->MRP, 2, '.', '');;
           $k->price=number_format((float)$k->price, 2, '.', '');;
           $k->selling_price=number_format((float)$k->selling_price, 2, '.', '');;
           $k->save();
       }
       echo "done";
    }
    public function newsletter(Request $request){
       $getemail=Newsletter::where("email",$request->get("newsletter"))->first();
       if($getemail){
            Session::flash('message1',__('messages_error_success.newsletter_note')); 
          Session::flash('alert-class', 'alert-success');
       }
       else{
          $store=new Newsletter();
          $store->email=$request->get("newsletter");
          $store->save();
          Session::flash('message1',__('messages_error_success.newsletter_note')); 
          Session::flash('alert-class', 'alert-success');
       }
        return redirect()->back();
    }
    public function home(){
        $setting=Setting::find(1);        
        if($setting->is_web=='2'){
            return redirect('login');
        }
        $deals=Deal::with("offer")->get();  
        $sepcat=Sepicalcategories::where("is_active","1")->first();    
        $sen_offer=Seasonaloffer::where("is_active","1")->first();  
        $setting=Setting::find(1);
        $shiping=Shipping::find(1);
        $res_curr=explode("-",$setting->default_currency);
        $bestselling=$this->getbestsellingpro();  
        $getcat=$this->getheadermenu();  
        $productdata=$this->getproductlist(); 
        $banner=Banner::all();
        $fdata=array();
        $featurepro=FeatureProduct::with('productdata')->orderby('id','DESC')->get();
        foreach ($featurepro as $k) {
            if($k->productdata->is_deleted=='0'){
                 $k->productdata->name=substr($k->productdata->name, 0,15);
                 $getreview=Review::where("product_id",$k->product_id)->where("is_deleted",'0')->get();
                 $k->total_review=count($getreview);
                 $avgStar = Review::where("product_id",$k->product_id)->avg('ratting');
                 $wish=Wishlist::where("product_id",$k->product_id)->where("user_id",Auth::id())->get();
                 $k->wish=count($wish);
                 $k->productdata->avgStar=round($avgStar);
                 $fdata[]=$k;
            }
            
        }

        $featurepro=$fdata;
         $date=date("Y-m-d");
         $best=array();
         $bestoffer=Offer::where("offer_type","1")->orderby('id',"DESC")->get();
         foreach ($bestoffer as $bo) {
          $start_date=date("Y-m-d",strtotime($bo->start_date)); 
          $end_date=date("Y-m-d",strtotime($bo->end_date));
          if(($date>=$start_date)&&($date<=$end_date)){
                  $best[]=$bo;
          }
        }
        $pay=PaymentMethod::find(1);
        $mywish=Wishlist::where("user_id",Auth::id())->get();
        return view("user.home")->with("header_menu",$getcat)->with("offerdata",$deals)->with("banner",$banner)->with("sepical_cat",$sepcat)->with("sen_offer",$sen_offer)->with("setting",$setting)->with("bestselling",$bestselling)->with("currency",Session::get("currency"))->with("productdata",$productdata)->with("featurepro",$featurepro)->with("mywish",$mywish)->with("bestoffer",$best);
  }
  
  
  public function productupdate(){      
         $product=Product::all();
         foreach ($product as $k) {
            if($k->special_price!=""){
                        $today = date('Y-m-d');
                        $start_date = date('Y-m-d', strtotime($k->special_price_start));
                        $end_date = date('Y-m-d', strtotime($k->special_price_to));
                        if($today>=$start_date&&$today<=$end_date){
                                $k->selling_price=(int)$k->special_price;
                                $dis_price=(int)($k->MRP)-(int)($k->special_price);
                                $disper=0;
                                if($dis_price!=0&&$dis_price>0){
                                    $disper=((int)$dis_price/(int)$k->MRP)*100;
                                }
                                $k->discount=(int)floor($disper);
                        }else{

                                $dis_price=(int)($k->MRP)-(int)($k->price);
                                $disper=0;
                                if($dis_price!=0&&$dis_price>0){
                                        $disper=((int)$dis_price/(int)$k->MRP)*100;
                                }
                                $k->discount=(int)floor($disper);
                                $k->selling_price=(int)$k->price;
                            }
                             $k->save();
                           }
            else{
                              $dis_price=(int)($k->MRP)-(int)($k->price);
                                $disper=0;
                                if($dis_price!=0&&$dis_price>0){
                                        $disper=((int)$dis_price/(int)$k->MRP)*100;
                                }
                                $k->discount=(int)floor($disper);
                                $k->selling_price=(int)$k->price;   
                                $k->save();
            }
                          
                          
         }
        
  }
    
  public function colorchange(Request $request){
      Session::put("site_color",$request->get("colorvalue"));
      Session::put("colorid",$request->get("color_id"));
      return $request->get("colorvalue");
  }  
  public function showoffers(){
      $date=date("Y-m-d");
      $best=array();
      $normal=array();
      $getcat=$this->getheadermenu();
      $productdata=$this->getproductlist(); 
      $mywish=Wishlist::where("user_id",Auth::id())->get();
      $getoffers=$this->getproductoffers(); 
      $bestoffer=Offer::where("offer_type","1")->orderby('id',"DESC")->get();
      foreach ($bestoffer as $bo) {
          $start_date=date("Y-m-d",strtotime($bo->start_date)); 
          $end_date=date("Y-m-d",strtotime($bo->end_date));
          if(($date>=$start_date)&&($date<=$end_date)){
                  $best[]=$bo;
          }
        }
      $normaloffer=Offer::where("offer_type","2")->orderby('id',"DESC")->get();
      foreach ($normaloffer as $bo) {
          $start_date=date("Y-m-d",strtotime($bo->start_date)); 
          $end_date=date("Y-m-d",strtotime($bo->end_date));
          if(($date>=$start_date)&&($date<=$end_date)){
              if($bo->is_product=='3'){
                $bo->coupon_code="";
                  $getcoupon=Coupon::find($bo->coupon_id);
                  if($getcoupon){
                      $bo->coupon_code=$getcoupon->code;
                  }
              }
            
                  $normal[]=$bo;
          }
      }
      $setting=Setting::find(1);
      return view("user.offers")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("getoffers",$getoffers)->with("bestoffer",$best)->with("normaloffer",$normal)->with("setting",$setting);
  }

  public function getproductoffers(){
      $date=date("Y-m-d"); 
      $data=array();
      $product=Product::where("special_price","!=","")->where("status","1")->where("is_deleted","0")->get();
      foreach ($product as $k) {
          $start_date=date("Y-m-d",strtotime($k->special_price_start)); 
          $end_date=date("Y-m-d",strtotime($k->special_price_to)); 
          if(($date>=$start_date)&&($date<=$end_date)){
              $k->name=substr($k->name, 0,15);
              $getreview=Review::where("product_id",$k->id)->get();
              $k->total_review=count($getreview);
              $avgStar = Review::where("product_id",$k->id)->avg('ratting');
              $k->avgStar=round($avgStar);
              $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
              $k->wish=count($wish);
              $k->disper=$k->discount;
              $k->price=$k->selling_price;
              $data[]=$k;
          } 
      } 
    return $data;
  }
  public function forgotpassword(Request $request){
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
          return 1;
      }else{
          return 0;
      }
  }

  public function resetpassword($code){
      $data=Resetpassword::where("code",$code)->get();
        if(count($data)!=0){
          return view('user.resetpwd')->with("id",$data[0]->user_id)->with("code",$code);
        }else{
          return view('user.resetpwd')->with("msg",__('messages.Code Expired'));
        }
  }
  public function resetnewpwd(Request $request){
    if($request->get('id')==""){
        return view('user.resetpwd')->with("msg",__('messages_error_success.pwd_reset'));
    }else{
        $user=User::find($request->get('id'));
        $user->password=$request->get('npwd');
        $user->save();
        $codedel=Resetpassword::where('user_id',$request->get("id"))->delete();
        return view('user.resetpwd')->with("msg",__('messages_error_success.pwd_reset'));
    }
  }

  public function checkout(Request $request){
      $pay=PaymentMethod::find(2);
      $getshipping=Shipping::all();
      Session::put("stripe_key",$pay->payment_key);
      Session::put("stripe_secert",$pay->payment_secret);
      $cartCollection = Cart::getContent(); 
      if($cartCollection->count()==0){
            return redirect("/");
      }
      $getproducttax=array();  
      $tax=array();  
      foreach ($cartCollection as $item) {
           $gettotal=array();
           $subtotal=$item->price*$item->quantity;
           $producttax=Product::where("name",$item->name)->first();
           $taxdata=Taxes::find($producttax->tax_class);
           $a=$taxdata->rate/100;
           $b=$subtotal*$a;
           $tax[]=$taxdata->tax_name;
           $gettotal['name']=$taxdata->tax_name;
           $gettotal['tax']=number_format((float)$b, 2, '.', '');
           $getproducttax[]=$gettotal;
      }
      $finalarray=array();
      foreach (array_unique($tax) as $k) {
           $total=0;
           foreach ($getproducttax as $ge) {
              if($k==$ge['name']){
                $total=$total+$ge['tax'];
              }
           }
           $finalarray[]=array("name"=>$k,"total"=>$total);
      }
      $getcat=$this->getheadermenu();
        if($request->get("check_delivery")==""){
           $delivery_type=1;
           $delivery_charge=0;
          
           if(count($getshipping)>0){
               Session::put("delivery_type",1);
               Session::put("delivery_charge",$getshipping[0]->cost);
           }
        }
        else{
            
            $ship=explode('#',$request->get("check_delivery"));
            $delivery_type=$ship[0];
            $delivery_charge=$ship[1];
            Session::put("delivery_type",$ship[0]);
            Session::put("delivery_charge",$ship[1]);
        }
       
        $productdata=$this->getproductlist(); 
        $paymet=PaymentMethod::all();
        $mywish=Wishlist::where("user_id",Auth::id())->get();   
          $setting=Setting::find(1);
        return view("user.product.checkout")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("delivery_type",$delivery_type)->with("check_discount_type",$request->get("check_discount_type"))->with("check_discount_value",$request->get("check_discount_value"))->with("check_free_shipping",$request->get("check_free_shipping"))->with("check_coupon_value",$request->get("check_coupon_value"))->with("check_couponcode",$request->get("check_couponcode"))->with("product_tax",$finalarray)->with("payment_method",$paymet)->with("delivery_charge",$delivery_charge)->with("check_delivery",$request->get("check_delivery"))->with("setting",$setting);
  }


    public function help(){
        $gettext=QueryTopic::with("Question")->where("page_id",'1')->get();      
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist(); 
        $mywish=Wishlist::where("user_id",Auth::id())->get();   
        $setting=Setting::find(1);
        return view("user.help")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("getquestion",$gettext)->with("setting",$setting);
    }

    public function mywishlist(){
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist(); 
        $setting=Setting::find(1);
        $mywish=Wishlist::with('productdata')->where("user_id",Auth::id())->get();   
        return view("user.account.wishlist")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("setting",$setting);
    }

    public function vieworder($id){
        $order=Order::with('userdata')->find($id);
        $orderdata=OrderData::with('productdata')->where("order_id",$id)->get();
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist(); 
        $setting=Setting::find(1);
        $mywish=Wishlist::with('productdata')->where("user_id",Auth::id())->get();   
        return view("user.account.vieworder")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("order",$order)->with("orderdata",$orderdata)->with("setting",$setting);
    }
   
    public function searchproduct(Request $request){    
      $ca=$request->get("search_cat");
      $se=$request->get("search_product");
      $findproduct=Product::where("name",$request->get("search_product"))->first();
      
        return redirect("productslist/0/0/0/0/"."?s=".$se."&ca=".$ca);
        
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
        foreach ($product as $k) {
            $k->fname=$k->name;
             $k->name=substr($k->name,0,15);
             $getreview=Review::where("product_id",$k->id)->where("is_deleted",'0')->get();
             $k->total_review=count($getreview);
             $avgStar = Review::where("product_id",$k->id)->avg('ratting');
             $wish=Wishlist::where("product_id",$k->id)->where("user_id",Auth::id())->get();
             $k->wish=count($wish);
             $k->avgStar=round($avgStar);
             
        }
        return $product;
    }
    public function termscondition(){
        $gettext=QueryTopic::with("Question")->where("page_id",'2')->get();
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist(); 
        $mywish=Wishlist::where("user_id",Auth::id())->get();      
        $setting=Setting::find(1);
        return view("user.terms")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("getquestion",$gettext)->with("setting",$setting);
    }
  public function aboutus(){
        $page=Pages::find(1);
        $mywish=Wishlist::where("user_id",Auth::id())->get();   
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist(); 
        $setting=Setting::find(1);
        return view("user.aboutus")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("page",$page)->with("setting",$setting);
    }

   
    public function contactus(){
      $getcat=$this->getheadermenu();
      $productdata=$this->getproductlist(); 
      $mywish=Wishlist::where("user_id",Auth::id())->get(); 
      $setting=Setting::find(1);  
      return view("user.contactus")->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("setting",$setting);
    }

    

    public function myaccount(){
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist();
        $user=Auth::user();
        $myorder=Order::where("user_id",Auth::id())->orderby("id","DESC")->get();
       
        foreach ($myorder as $k) {
            $getdata=OrderData::where('order_id',$k->id)->get();
          
             $item=0;
            foreach($getdata as $ge){
                $item=$item+$ge->quantity;
            }

            $k->total_item=$item;
        }
       
        Session::put("profile_pic",$user->profile_pic);
        Session::put("name",$user->first_name);
        Session::put("email",$user->email);
        $setting=Setting::find(1);
         $mywish=Wishlist::where("user_id",Auth::id())->get();
        return view("user.account.myaccount")->with("header_menu",$getcat)->with("userdata",$user)->with("productdata",$productdata)->with("mywish",$mywish)->with("myorder",$myorder)->with("setting",$setting);
    }
    
    public function storecontact(Request $request){
        $store=new ContactUs();
        $store->name=$request->get("name");
        $store->email=$request->get("email");
        $store->subject=$request->get("subject");
        $store->phone=$request->get("phone");
        $store->message=$request->get("message");
        $store->save();
        Session::flash('message',__('messages.contact_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }
       public function viewproduct($id){
          $product=$this->getproductdetails($id);   
          
          if(empty($product)){
             return redirect("/");
          }      
          $setting=Setting::find(1);   
          $user=Auth::user();   
          $res_curr=explode("-",$setting->default_currency);                   
          $getcat=$this->getheadermenu();
          $productdata=$this->getproductlist();
          $prorat=0;
          $avgStar = Review::where("product_id",$id)->avg('ratting');
          if(empty($avgStar)){
             $avgStar=0;
          }
          else{
             $avgStar=round($avgStar);
          }
          $name=strip_tags($product->description);
          $product->new_desc=substr($name,0,175);
          
          $product->wish=count(Wishlist::where("product_id",$id)->where("user_id",Auth::id())->get());
          $product->totalreview=count(Review::where("product_id",$id)->get());
           $mywish=Wishlist::where("user_id",Auth::id())->get();
          $setting=Setting::find(1);
          return view("user.product.viewproduct")->with("header_menu",$getcat)->with("product",$product)->with("currency",$res_curr[1])->with("userdata",$user)->with("productstar",$avgStar)->with("productdata",$productdata)->with("mywish",$mywish)->with("setting",$setting);;
       }

public function getproductdetails($id){
    $product=Product::with("categoryls","subcategoryls","brandls")->where('status','1')->where('is_deleted','0')->where("id",$id)->first();
          if(!empty($product)){
           $product->category_id=$product->category;
          $product->subcategory_id=$product->subcategory;
          $product->brand_id=$product->brand;
          $product->category=$product->categoryls->name;
          $product->subcategory=$product->subcategoryls->name;
          $product->brand=$product->brandls->brand_name;
         
          $main_array=array();
          $attributearr=array();
          $attribute_set=array();
          $data=array();
          if($product->related_product!=""){
              $relat=explode(",",$product->related_product);          
              foreach ($relat as $ky) {
              	     $avgStar = Review::where("product_id",$ky)->avg('ratting');
                     if(empty($avgStar)){
                         $avgStar=0;
                      }
                     else{
                         $avgStar=round($avgStar);
                      }
                      $wish=Wishlist::where("product_id",$ky)->where("user_id",Auth::id())->get();
                      $ts=Product::find($ky);
                      $ts->avgStar=$avgStar;
                      $ts->total_review=count(Review::where("product_id",$ky)->get());
                      
                      $ts->name=$this->headreadMoreHelper($product->name,25);
                      $ts->wish=count($wish);
                      
                  $data[]=$ts;
              }
              $product->related_product=$data;
          }
         
          $product->attributes=ProductAttributes::where("product_id",$id)->get();
          
        
          $product->options=ProductOption::where("product_id",$id)->first();
          $product->review=Review::with('userdata')->where("product_id",$id)->where("is_deleted",'0')->orderby("id","DESC")->orwhere("user_id",Auth::id())->take(5)->get();
          }
          $avgStar = Review::where("product_id",$id)->avg('ratting');
          if(empty($avgStar)){
             $avgStar=0;
          }
          else{
             $avgStar=round($avgStar);
          }
        
          if($product){
              $product->avgStar=$avgStar;
          }
          else{
              //$product->avgStar=0;
          }
          
          return $product;
       }

public function getbestsellingpro(){
    $main_array=array();
    $orderdata=DB::table('order_data')
                 ->select('product_id', DB::raw('count(*) as total'))
                 ->orderby('total','DESC')
                 ->groupBy('product_id')                 
                 ->paginate(10);
    foreach ($orderdata as $p) {
          $product=Product::where('status','1')->where('is_deleted','0')->where("id",$p->product_id)->first();
          $avgStar = Review::where("product_id",$p->product_id)->avg('ratting');
          $total=count(Review::where("product_id",$p->product_id)->get());
          $wish=Wishlist::where("product_id",$p->product_id)->where("user_id",Auth::id())->get();
          if($product){
                $array_ob["basic_image"]=$product->basic_image;
                $array_ob["name"]=substr($product->name,0,15);
                $array_ob["price"]=$product->selling_price;
                $array_ob["rating"]=round($avgStar);
                $array_ob["id"]=$product->id;
                $array_ob['ratting']=$avgStar;
                $array_ob['wish']=count($wish);
                $array_ob['MRP']=$product->MRP;
                $array_ob['discount']=$product->discount;
                $array_ob['total_review']=$total;
                $array_ob['stock']=$product->stock;
                $main_array[]=$array_ob;
            }     
          }
        
    return $main_array;             
}
function headreadMoreHelper($story_desc, $chars =35) {
    $story_desc = substr($story_desc,0,$chars);  
    $story_desc = substr($story_desc,0,strrpos($story_desc,' '));  
    $story_desc = $story_desc;  
    return $story_desc;  
}  

function readMoreHelper($story_desc, $chars =35) {
    $story_desc = substr($story_desc,0,$chars);  
    $story_desc = substr($story_desc,0,strrpos($story_desc,' '));  
    $story_desc = $story_desc.'...';  
    return $story_desc;  
} 

     

public function gethelpresult(Request $request){
      $gettopic=QueryTopic::with("Question")->where("topic",$request->get("search"))->where("page_id","1")->get();
       if(count($gettopic)!=0){
          return $gettopic[0]->id."-"."0";
       }
       else{
          $getque=QueryAns::with("query_id")->where("question",$request->get("search"))->get();
           if(count($getque)!=0){
             return $getque[0]->topic_id."-".$getque[0]->id;
           }
       }
       return "0";
    }

  public function productmod(){
     $product=Product::all();
     foreach ($product as $k) {
        $k->MRP=round($k->MRP/70);
        $k->price=round($k->price/70);
        if($k->selling_price!=""){
            $k->selling_price=round($k->selling_price/70);
        }
        $k->save();        
     }
     echo "done";

  }    

  public function getallcategory(){
     $category=Categories::where("parent_category",0)->where("is_delete",'0')->get();
     return json_encode($category);
  }


  public function getcompare(){
      $datalist=array();
      $attr_name=array();
       if(Session::get("compare")){
            $arry=explode(",",Session::get("compare"));
            $datalist=array();
            foreach ($arry as $key) {
                $main_array=array();
                $product=Product::select("id","name","basic_image","selling_price","sku","stock")->find($key);
                $product->attributes=ProductAttributes::where("product_id",$key)->get();
                $datalist[]=$product;
            }
            
             
              $attribute_set=array();
              foreach ($datalist as $k) {                    
                    foreach ($k->attributes as $att) {
                        $attribute_set[]=$att["attributename"];
                    }
                    $k->description=$this->headreadMoreHelper(html_entity_decode($k->description),90);
              }
              $attr_name=array();
              foreach (array_filter($attribute_set) as $dt) {
                $attr_name[]=$dt['name'];
              }
              $attr_name=array_values(array_unique($attr_name));
          
       }

        $mywish=Wishlist::where("user_id",Auth::id())->get();   
        $getcat=$this->getheadermenu();
        $productdata=$this->getproductlist(); 
        $setting=Setting::find(1);
        return view("user.compare")->with("comparedata",$datalist)->with("header_menu",$getcat)->with("productdata",$productdata)->with("mywish",$mywish)->with("attr_name",$attr_name)->with("setting",$setting);;
    }


    public function addcomapreitem($id){
       if(Session::get("compare")!=""){
           $arr=explode(',', Session::get("compare"));
           $arr[]=$id;
           Session::put("compare",implode(",",array_values(array_unique($arr))));
           return count(array_values(array_unique($arr)));
       }
       else{
            Session::put("compare",$id);
            return 1;
       }
    }

    public function deletecomapreitem($id){
        if(Session::get("compare")!=""){
           $arr=explode(',', Session::get("compare"));          
           unset($arr[array_search($id,$arr)]);
           
           Session::put("compare",implode(",",array_values(array_unique($arr))));
           return count(array_values(array_unique($arr)));
       }
       else{
            Session::put("compare","");
            return 0;
       }
    }
}
