<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use App\User;
use Session;
use App\Model\Setting;
use App\Model\Review;
use App\Model\Wishlist;
use App\Model\OrderResponse;
use App\Model\OrderData;
use App\Model\Order;
use DataTables;
Use Image;
Use Mail;
use Hash;
use DB;
use Auth;
class UserController extends Controller {
     public function __construct() {
         parent::callschedule();
    }
    public function storewishlist(Request $request){
      $checkuser=Wishlist::where("product_id",$request->get("product_id"))->where("user_id",$request->get("user_id"))->get();
      if(count($checkuser)==0){
           $wish=new Wishlist();
           $wish->user_id=$request->get("user_id");
           $wish->product_id=$request->get("product_id");
           $wish->save();
      }
      $totalwish=Wishlist::where("user_id",$request->get("user_id"))->get();
      return count($totalwish);
    }

    public function userdelete($id){
        $user=User::find($id);
        $user->delete();
        $order=Order::where("user_id",$id)->get();
        foreach ($order as $k) {
            $order=OrderResponse::where("order_id",$k->id)->delete();
            $order=OrderData::where("order_id",$k->id)->delete();
            $k->delete();
        }
         $delreview=Review::where("user_id",$id)->delete();
         Session::flash('message',__('messages_error_success.user_del')); 
         Session::flash('alert-class', 'alert-success');
         return redirect()->back();
    }

    public function saveaddress(Request $request){
        $fields=$request->get("fields");
       $store=Auth::user();
       $store->$fields=$request->get('address');
       $store->save();
       return "done";
    }

    public function deletewishlist(Request $request){
       $checkuser=Wishlist::where("product_id",$request->get("product_id"))->where("user_id",$request->get("user_id"))->delete();
       $getwish=Wishlist::with('productdata')->where("user_id",$request->get("user_id"))->get();
     
       $txt='<tr class="pro-heading" style="background:'.Session::get("site_color").' !important"><th>'.__("messages.del").'</th><th>'.__("messages.images").'</th><th>'.__("messages.product").'</th><th>'.__("messages.stock_status").'</th><th>'.__("messages.price").'</th><th></th></tr>';
       if(count($getwish)!=0){
           foreach($getwish as $mw){
                   $txt=$txt.'<tr><td class="Delete-icon"><a href="javascript:;" onclick="deletewish('.$mw->product_id.')"><i class="fa fa-trash-o" aria-hidden="true"></i></a><span>'.__('messages.del').':</span></td><td class="cart-img"><img src='.asset('public/upload/product').'/'.$mw->productdata->basic_image.'><span>'.__('messages.images').' :</span></td><td class="place-text"><div class="text-a"><span>'.__('messages.product').' :</span><h1>'.$mw->productdata->name.'</h1></div></td><td class="Stock-text">';
                   if($mw->productdata->stock=='0'){
                       $txt=$txt.__("messages.outstock");
                   }
                   else{
                       $txt=$txt.__("messages.in_stock");
                   }
                   $txt=$txt.'<span>'.__('messages.stock_status').':</span></td><td class="price">'.Session::get('currency').$mw->productdata->price.'<span>'.__('messages.price').':</span></td><td class="add"><a onclick="addwishtocart('.$mw->product_id.',' . "'" . $mw->productdata->name. "'" . ',1,'.$mw->productdata->price.')" style="border-color:'.Session::get("site_color").'!important">'.__('messages.add_to_cart').'</a></td></tr>';
           }
       }
       else{
           $txt=$txt.'<tr><td colspan="6" class="emptywish">'.__('messages.Your wishlist is currently empty!').'</td></tr>';
       }
       $data=array("content"=>$txt,"total"=>count($getwish));
       return json_encode($data);
    }
    
    public function index(){
       return view("admin.user.default");
    }
    
    public function indexadmin(){
      return view("admin.user.admin");
    }

    public function saveuserreview(Request $request){
        $user=Auth::user();
        $store=new Review();
        $store->product_id=$request->get("product_id");
        $store->user_id=$user->id;
        $store->ratting=$request->get("ratting");
        $store->review=$request->get("review");
        $store->save();
        return __('messages_error_success.review_success');

    }

    public function userlogin(Request $request){
          $setting=Setting::find(1);
          $checkuser=User::where("email",$request->get("email"))->where("password",$request->get("password"))->first();
          if($checkuser){
               
                Auth::login($checkuser, true);
                $data=Auth::user();
                if($request->get("rem_me")==1){
                    setcookie('user_email', $request->get("email"), time() + (86400 * 30), "/");
                    setcookie('password',$request->get("password"), time() + (86400 * 30), "/");
                   setcookie('rem_me',1, time() + (86400 * 30), "/");
               } 
                return "done";
          }
        else{
            return __('messages_error_success.login_error');
        } 
    }


    public function userregister(Request $request){
        $setting=Setting::find(1);    
        $checkemail=User::where("email",$request->get("email"))->first();
        if(empty($checkemail)){
           DB::beginTransaction();
              try {
                    $user=new User();
                    $user->first_name=$request->get("first_name");
                    $user->email=$request->get("email");
                    $user->password=$request->get("password");
                    $user->is_email_verified='1';
                    $user->address=$request->get("address");
                    $user->phone=$request->get("phone");
                    $user->login_type=1;
                    $user->user_type='1';                                    
                    $user->save();
                    try {
                        if($setting->customer_reg_email=='1'){
                            Mail::send('email.register_confirmation', ['user' => $user], function($message) use ($user){
                                                     $message->to($user->email,$user->first_name)->subject('shop on');
                                    });
                        }
                    } catch (\Exception $e) {
                    }
                    DB::commit();
                    return "done";
              }
              catch (\Exception $e) {
                   DB::rollback();
                   return __('messages_error_success.error_code');      
              }          
        }
        else{
            return __('messages_error_success.email_already_error');
        }
    }

    public function userdatatable($id){
         $user =User::where('user_type',$id)->orderBy('id','DESC')->get();
         return DataTables::of($user)
            ->editColumn('id', function ($user) {
                return $user->id;
            })
            ->editColumn('name', function ($user) {
                return $user->first_name;
            })
            ->editColumn('email', function ($user) {
                return $user->email;
            })
            ->editColumn('phone', function ($user) {
                return $user->phone;
            })            
            ->editColumn('action', function ($user) {
               $changestatus=url('admin/changeuserstatus',array('id'=>$user->id));
                $deleteuser=url('admin/userdelete',array('id'=>$user->id));
               if($user->is_active=='1'){
                    $color="green";
                 }
                 else{
                    $color="red";
                 }
                 $return = '<a onclick="edituser('.$user->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#edituser"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a href="'.$changestatus.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a>';
                 return $return;              
            })           
            ->make(true);
    }

    public function adduser(Request $request){  
        $setting=Setting::find(1);    
        $checkemail=User::where("email",$request->get("email"))->first();
        if(empty($checkemail)){
           DB::beginTransaction();
              try {
                    if($request->get("user_type")==1){
                        $user=new User();
                        $user->email=$request->get("email");
                        $user->password=$request->get("password");
                    }
                    else{
                        $user = Sentinel::registerAndActivate($request->input());
                    }
                    
                    $user->first_name=$request->get("first_name");
                    $user->is_email_verified='1';
                    $user->address=$request->get("address");
                    $user->phone=$request->get("phone");
                    $user->login_type=1;
                    $user->user_type=$request->get("user_type");                                    
                    $user->save();
                    
                          DB::commit();
                           Session::flash('message',__('messages_error_success.create_success')); 
                                    Session::flash('alert-class', 'alert-success');
                                    return redirect()->back();
                          
              }
              catch (\Exception $e) {
                   DB::rollback();
                   Session::flash('message',__('messages_error_success.error_code')); 
                   Session::flash('alert-class', 'alert-danger');
                   return redirect()->back();       
              }          
        }
        else{
            Session::flash('message',__('messages_error_success.email_already_error')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
        
    }

    public function changestatus($id){
        $store=User::find($id);
        if($store->is_active=='0'){
            $store->is_active='1';
            $msg=__('messages_error_success.user_active_msg');
        }
        else{
            $store->is_active='0';
            $msg=__('messages_error_success.user_deactive_msg');
        }
        $store->save();
        Session::flash('message',$msg); 
        Session::flash('alert-class', 'alert-success');
        return redirect("admin/user");
    }

    public function edituser($id){
        $data=User::find($id);
        return json_encode($data);
    }

    public function updateuser(Request $request){

      $data=User::find($request->get("id"));
      $data->first_name=$request->get("first_name");
      $data->email=$request->get("email");
      $data->phone=$request->get("phone");
      $data->address=$request->get("address");
      $data->save();
      Session::flash('message',__('messages_error_success.user_update_success')); 
      Session::flash('alert-class', 'alert-success');
      return redirect()->back();
    }

    public function userrole(){
       return view("admin.user.role");
    }
 
    public function confirmregister($id){
        $store=User::find($id);
        $store->is_email_verified='1';
        $store->save();
        Session::flash('message',__('messages_error_success.email_verified')); 
        Session::flash('alert-class', 'alert-success');
        return view("emailverified");
    }
}