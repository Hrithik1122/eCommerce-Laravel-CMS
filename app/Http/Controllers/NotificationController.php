<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Model\Notification;
use App\Model\Setting;
use App\Model\Token;
Use Image;
use Hash;
class NotificationController extends Controller {
  

    public function index(){
    	return view("admin.notification");
    }

    public function notificationTable(){
    	 $notification =Notification::all();
            return DataTables::of($notification)
                ->editColumn('id', function ($notification) {
                   return $notification->id;
                })
                ->editColumn('msg', function ($notification) {
                   return $notification->msg;
                }) 
              
                           
            ->make(true);
    }

    public function addsendnotification(Request $request){
    	$setting=Setting::find(1);
    	$android=$this->send_notification_android($setting->android_api_key,$request->get("msg"));
    	$ios=$this->send_notification_IOS($setting->iphone_api_key,$request->get("msg"));
    	if($android==1||$ios==1){
    		$store=new Notification();
    		$store->msg=$request->get("msg");
    		$store->save();
    		Session::flash('message',__('messages.success_notification')); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
    	}
    	else{
    		Session::flash('message',__('messages.error_notification')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
    	}

    }
     public function send_notification_android($key,$msg){

          $getuser=Token::where("type",1)->get();
        if(count($getuser)!=0){               
               $reg_id = array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
               $regIdChunk=array_chunk($reg_id,1000);
               $response=array();
               foreach ($regIdChunk as $k) {
                       $registrationIds =  $k; 
                       $message = array(
                            'message' => $msg,
                            'key'=>'normal',
                            'title' => __('messages.order_status'));
                      
                       $fields = array(
                          'registration_ids'  => $registrationIds,
                          'data'              => $message
                       );
        
                       $url = 'https://fcm.googleapis.com/fcm/send';
                       $headers = array(
                         'Authorization: key='.$key,// . $api_key,
                         'Content-Type: application/json'
                       );
                      $json =  json_encode($fields);   
                      try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
                            $result = curl_exec($ch);  
              
                            if ($result === FALSE){
                               die('Curl failed: ' . curl_error($ch));
                            }     
                           curl_close($ch);
                           $response[]=json_decode($result,true);
                      } catch (\Exception $e) {
                       
                      }
                }
               $succ=0;
               foreach ($response as $k) {
                   if(isset($k['success'])){
                        $succ=$succ+$k['success'];
                   }
                 
               }
              if($succ>0)
              {
                   return 1;
              }
              else
              {
                  return 0;
              }
        }
        return 0;
   }
   public function send_notification_IOS($key,$msg){
      $getuser=Token::where("type",2)->get();
         if(count($getuser)!=0){               
               $reg_id = array();
               $response=array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
               $regIdChunk=array_chunk($reg_id,1000);
               foreach ($regIdChunk as $k) {
                        $registrationIds =  $k;    
                        $message = array(
                           'body'  => $msg,
                           'title'     => __('messages.notification'),
                           'vibrate'   => 1,
                           'sound'     => 1,
                           'key'=>'normal'
                       );
                       $fields = array(
                          'registration_ids'  => $registrationIds,
                          'data'              => $message
                       );
        
                       $url = 'https://fcm.googleapis.com/fcm/send';
                       $headers = array(
                         'Authorization: key='.$key,// . $api_key,
                         'Content-Type: application/json'
                       );
                      $json =  json_encode($fields);   
                       try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
                            $result = curl_exec($ch);  
              
                            if ($result === FALSE){
                               die('Curl failed: ' . curl_error($ch));
                            }     
                           curl_close($ch);
                           $response[]=json_decode($result,true);
                      } catch (\Exception $e) {
                       
                      }
               }
               $succ=0;
               foreach ($response as $k) {
                  if(isset($k['success'])){
                        $succ=$succ+$k['success'];
                   }
               }
              if($succ>0)
              {
                   return 1;
              }
              else
               {
                  return 0;
               }
        }
        return 0;
   }
}