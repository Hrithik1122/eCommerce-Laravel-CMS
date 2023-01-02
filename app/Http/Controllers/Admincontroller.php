<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use App\Model\Order;
use App\Model\Setting;
use App\Model\Product;
use Session;
use DataTables;
use App\User;
use Hash;
use Auth;
class Admincontroller extends Controller {
  
   public function showlogin(){
       parent::callschedule();
       $setting=Setting::find(1);
       Session::put("is_demo",$setting->is_demo);
       Session::put("is_rtl",$setting->is_rtl);
       Session::put("is_web",$setting->is_web); 
       return view("admin.login");
   }
   
   public function privacy(){
       return view("privacy_policy");
   }

   public function showdoc(){
      return view("document.layout");
   }

   public function adddoc(){
      return view("document.product");
   }
   
   public function dealofferdoc(){
       return view("document.dealoffer");
   }
 
   public function normalofferdoc(){
       return view("document.normaloffer");
   }
   
   public function mainofferdoc(){
       return view("document.mainoffer");
   }
   public function addproductstep(){
      return view("document.addproductstep");
   }
   
   public function addfeadoc(){
      return view('document.addfeadoc');
   }
   public function postlogin(Request $request){
        $checkuser=User::where("email",$request->get("email"))->where("user_type",'2')->get();
        if(count($checkuser)!=0){
             $user=Sentinel::authenticate($request->all());
             if($user){
                 $data=Sentinel::getUser();
                 Session::put("profile_pic",asset("public/upload/profile/"."/".$data->profile_pic));
                 return  redirect("admin/dashboard");    
               } 
              else{
                   
                    Session::flash('message',__('messages_error_success.login_error')); 
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->back();
              } 
        }
        else{
            Session::flash('message',__('messages_error_success.login_error')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }       
   }

   public function showdashboard(){
        $setting=Setting::find(1);
        $res_curr=explode("-",$setting->default_currency);
        $order=Order::all();
        if(count($order)!=0){
             $array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
        }
        else{
             $array_ob["date"]="";
        }
         
          $array_ob["order"]=count($order);
          $subtotal=0;
          $shipping=0;
          $product=array();
          $tax=0;
          $total=0;
          foreach ($order as $k) {
              $subtotal=(float)$subtotal+(float)$k->subtotal;
              $shipping=(float)$shipping+(float)$k->shipping_charge;
              $tax=(float)$tax+(float)$k->taxes_charge;
          }
          $total=$tax+$shipping+$subtotal;
          $total_sell=$res_curr[1]."".$total;
          $total_order=Order::all();
          $total_product=Product::all();
          $total_customer=User::where("user_type",'1')->get();
          return view("admin.dashboard")->with("total_order",count($total_order))->with("total_product",count($total_product))->with("total_users",count($total_customer))->with("total_sell",$total_sell);
   }

   public function showlogout(){
            Sentinel::logout();
            return redirect('login');
   }

   public function showuserlogout(){
        Auth::logout();
        Session::forget('user_id');
        return redirect('/');
   }

   public function editprofile(){
        $user=Sentinel::getUser();
       return view("admin.updateprofile")->with("data",$user);
   }

   public function updateprofile(Request $request){      
           $user=Sentinel::getUser();
           if ($request->hasFile('file')) 
              {
                 $file = $request->file('file');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/profile';
                 $picture = str_random(10).time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('file')->move($destinationPath, $picture);
                 $img_url =$picture;
             }else{
                 $img_url = $user->profile_pic;
             }
            $data=User::find($user->id);
            $data->first_name=$request->get("name");
            $data->profile_pic=$img_url;
            $data->save();
            Session::put("profile_pic",asset("public/upload/profile/"."/".$img_url));
            Session::flash('message',__('messages_error_success.profile_sucess_update')); 
            Session::flash('alert-class', 'alert-success');
            return redirect("admin/editprofile");
   }

    public function changepassword(Request $request){
      return view("admin.changepassword");
   }  
   public  function check_password_same($pwd){
    $user=Sentinel::getUser();
     if (Hash::check($pwd, $user->password))
     {
        $data=1;
     }
    else{
        $data=0; 
     }
   return json_encode($data);
   }
   
   public function check_user_password_same($pwd){
       $user=Auth::user();
       if($user->password==$pwd)
       {
          $data=1;
       }
       else{
            $data=0; 
       }
       return json_encode($data);
   }
   public function updatepassword(Request $request){
     $user=Sentinel::getUser();
       if (Hash::check($request->get('cpwd'), $user->password))
        {
            Sentinel::update($user, array('password' => $request->get('npwd')));
            Session::flash('message',__("messages_error_success.password_update_success")); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
        }
       else{
          Session::flash('message',__('messages_error_success.error_code')); 
          Session::flash('alert-class', 'alert-danger');
          return redirect()->back();;
       }
       
   }

   public function changeuserpwd(Request $request){
      $user=Auth::user();
      $user->password=$request->get('npwd');
      $user->save();
      return __("messages_error_success.password_update_success");
   }

   public function updateuserprofile(Request $request){
         
            if ($request->hasFile('file')) 
              {
                 $file = $request->file('file');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/profile';
                 $picture = str_random(10).time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('file')->move($destinationPath, $picture);
                 $img_url =$picture;
             }else{
                 $img_url = Auth::user()->profile_pic;
             }
            $data=User::find(Auth::id());
            $data->first_name=$request->get("edit_first_name");
            $data->phone=$request->get("edit_phone");
            $data->address=$request->get("edit_address");
            $data->profile_pic=$img_url;
            $data->save();
            Session::put("profile_pic",asset("public/upload/profile/"."/".$img_url));
            Session::put("name",$data->first_name);
            Session::flash('message',__('messages_error_success.profile_sucess_update')); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
   }
}