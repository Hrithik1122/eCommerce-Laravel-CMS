<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use App\Model\Pages;
use App\Model\Shipping;
use App\Model\Setting;
use App\Model\Languages;
use App\Model\Country;
use App\Model\PaymentMethod;
use DateTimeZone;
use DateTime;
use DataTables;
use Image;
use Artisan;
use Hash;
class SettingController extends Controller {
       public function __construct() {
         parent::callschedule();
    }
     public function indexpage(){
         return view("admin.setting.page");
     }

     public function getcountrylist(){
        $country=Country::orderBy("nicename")->get();
        return json_encode($country);
     }

     public function serverkey($id){
       $setting=Setting::find(1);      
       $key="";
       if($id==1){

            $key=$setting->android_api_key;
       }
       if($id==2){
            $key=$setting->iphone_api_key;
       }
      
       return view("admin.setting.serverkey")->with("serverkey",$key)->with("id",$id);
     }

     public function updatekey(Request $request){
           $setting=Setting::find(1);
           if($request->get("id")==1){
                $setting->android_api_key=$request->get("serverkey");
           }
           if($request->get("id")==2){
                $setting->iphone_api_key=$request->get("serverkey");
           }
           $setting->save();
           Session::flash('message', __('messages.Key Update Successfully')); 
           Session::flash('alert-class', 'alert-success');
           return redirect()->back();
     }

     public function getlanglist(){
       $lang=Languages::orderBy("name")->get();
       return json_encode($lang);
     }

     public function pagedatatable(){
          $page =Pages::orderBy('id','DESC')->get();
            return DataTables::of($page)
                ->editColumn('id', function ($page) {
                   return $page->id;
                })
                ->editColumn('name', function ($page) {
                   return $page->page_name;
                })        
                ->editColumn('action', function ($page) {
                     $edit=url('admin/editpage',array('id'=>$page->id));
                     return '<a href="'.$edit.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a>';      
                })           
            ->make(true);
     }

     public function editpage($id){
        $data=Pages::find($id);
        return view("admin.setting.editpage")->with("data",$data);
     }

     public function updatepage(Request $request){
         $store=Pages::find($request->get("id"));
         $store->page_name=$request->get("page_name");
         $store->description=$request->get("description");
         $store->save();
         Session::flash('message', __('messages_error_success.page_update_success')); 
         Session::flash('alert-class', 'alert-success');
         return redirect()->back();

     }

     public function showshipping(){
            return view("admin.setting.shipping");
     }

     public function shippingdatatable(){
        $shipping =Shipping::orderBy('id','DESC')->get();
            return DataTables::of($shipping)
                ->editColumn('id', function ($shipping) {
                   return $shipping->id;
                })
                ->editColumn('label', function ($shipping) {
                   return $shipping->label;
                }) 
                ->editColumn('cost', function ($shipping) {
                   return $shipping->cost;
                })        
                ->editColumn('action', function ($shipping) {
                     if($shipping->is_enable=='1'){
                          $color="green";
                     }
                     else{
                          $color="red";
                     }
                     $status=url('admin/changeshipping_status',array('id'=>$shipping->id));
                     return '<a onclick="editshipping('.$shipping->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editshipping"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a href="'.$status.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a>';      
                })           
            ->make(true);
     }

     public function changeshipping($id){
        $data=Shipping::find($id);
        if($data->is_enable=='1'){
            $data->is_enable='0';
        }
        else{
            $data->is_enable='1';
        }
        $data->save();
        Session::flash('message',__('messages_error_success.status_change_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
     }

     public function editshipping($id){
        $data=Shipping::find($id);
        return json_encode($data);
     }

     public function updateshipping(Request $request){
            $data=Shipping::find($request->get("id"));
            $data->label=$request->get("label");
            $data->cost=$request->get("cost");
            $data->save();
            Session::flash('message',__('messages_error_success.status_change_success')); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
     }  

     public function editsetting(){
          $data=Setting::find(1);
          $country=Country::all();
          $lang=Languages::all();
          $timezone=$this->generate_timezone_list();
          $currency=$this->get_currency_list();
          $paymentmethod=PaymentMethod::all();        
          return view("admin.setting.setting")->with("data",$data)->with("country",$country)->with("lang",$lang)->with("timezone",$timezone)->with("currency",$currency)->with("paymentmethod",$paymentmethod);
     }

     public function savepaymentdata(Request $request){
         $store=PaymentMethod::find($request->get("id"));
         $store->label=$request->get("label");
         $store->status=$request->get("status");
         $store->description=$request->get("description");
         $store->payment_key=$request->get("key");
         $store->payment_secret=$request->get("secret");
         $store->payment_mode=$request->get("paymentmode");
         $store->save();
         return "done";
     }

     public function updatesetting(Request $request){
         $setting=Setting::find(1);
         $file_name=$setting->logo;
         $img=$setting->logo;
        if($request->get("logo")){
            $data = $request->get("logo");
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $folderName = '/Ecommerce/images/';
            $destinationPath = public_path() . $folderName;
            $file_name=uniqid() . '.png';
            $file = $destinationPath .$file_name;
            $data = base64_decode($data);
            file_put_contents($file, $data);
        }
        
         $setting->address=$request->get("address");
         $setting->email=$request->get("email");
         $setting->phone=$request->get("phone");
         $setting->default_country=$request->get("default_country");
         $setting->default_locale=$request->get("default_locale");
         $setting->default_timezone  =$request->get("timezone");
         $setting->default_currency=$request->get("currency");
         $setting->customer_order_status=$request->get("is_customer_order");
         $setting->customer_reg_email=$request->get("is_email_confirm");
         $setting->admin_order_mail=$request->get("is_admin_send_mail");
         $setting->working_day=$request->get("working_day");
         $setting->helpline=$request->get("helpline");
         $setting->main_feature=$request->get("main_feature");
         $setting->newsletter=$request->get("newsletter");
         $setting->company_name=$request->get("company_name");
         $setting->logo=$file_name;
         $setting->save();
         if($img!=$file_name){
                        $image_path="";
                        $image_path = public_path() ."/Ecommerce/images/".$img;
                        if(file_exists($image_path)) {
                            try{
                                 unlink($image_path);
                            }
                            catch(\Exception $e)
                            {
                                
                            }
                            
                        }
                }
         return "done";
     }

     public function savesoicalsetting(Request $request){
          $setting=Setting::find(1);        
          $setting->facebook_id=$request->get("facebook_id");
          $setting->facebook_secret=$request->get("facebook_secret");
          $setting->google_id=$request->get("google_id");
          $setting->google_secret=$request->get("google_secret");
          $setting->google_active=$request->get("is_google_required");
          $setting->facebook_active=$request->get("is_facebook_required");
          $setting->save();
         
          return "done";
     }
     
     
     static public function get_currency_list(){
        $currency_symbols = array(
            'AED' => '&#1583;.&#1573;',
            'AFN' => '&#65;&#102;',
            'ALL' => '&#76;&#101;&#107;',
            'ANG' => '&#402;',
            'AOA' => '&#75;&#122;',
            'ARS' => '&#36;',
            'AUD' => '&#36;',
            'AWG' => '&#402;',
            'AZN' => '&#1084;&#1072;&#1085;',
            'BAM' => '&#75;&#77;',
            'BBD' => '&#36;',
            'BDT' => '&#2547;',
            'BGN' => '&#1083;&#1074;',
            'BHD' => '.&#1583;.&#1576;',
            'BIF' => '&#70;&#66;&#117;',
            'BMD' => '&#36;',
            'BND' => '&#36;',
            'BOB' => '&#36;&#98;',
            'BRL' => '&#82;&#36;',
            'BSD' => '&#36;',
            'BTN' => '&#78;&#117;&#46;',
            'BWP' => '&#80;',
            'BYR' => '&#112;&#46;',
            'BZD' => '&#66;&#90;&#36;',
            'CAD' => '&#36;',
            'CDF' => '&#70;&#67;',
            'CHF' => '&#67;&#72;&#70;',
            'CLP' => '&#36;',
            'CNY' => '&#165;',
            'COP' => '&#36;',
            'CRC' => '&#8353;',
            'CUP' => '&#8396;',
            'CVE' => '&#36;',
            'CZK' => '&#75;&#269;',
            'DJF' => '&#70;&#100;&#106;',
            'DKK' => '&#107;&#114;',
            'DOP' => '&#82;&#68;&#36;',
            'DZD' => '&#1583;&#1580;',
            'EGP' => '&#163;',
            'ETB' => '&#66;&#114;',
            'EUR' => '&#8364;',
            'FJD' => '&#36;',
            'FKP' => '&#163;',
            'GBP' => '&#163;',
            'GEL' => '&#4314;',
            'GHS' => '&#162;',
            'GIP' => '&#163;',
            'GMD' => '&#68;',
            'GNF' => '&#70;&#71;',
            'GTQ' => '&#81;',
            'GYD' => '&#36;',
            'HKD' => '&#36;',
            'HNL' => '&#76;',
            'HRK' => '&#107;&#110;',
            'HTG' => '&#71;',
            'HUF' => '&#70;&#116;',
            'IDR' => '&#82;&#112;',
            'ILS' => '&#8362;',
            'INR' => '&#8377;',
            'IQD' => '&#1593;.&#1583;',
            'IRR' => '&#65020;',
            'ISK' => '&#107;&#114;',
            'JEP' => '&#163;',
            'JMD' => '&#74;&#36;',
            'JOD' => '&#74;&#68;',
            'JPY' => '&#165;',
            'KES' => '&#75;&#83;&#104;',
            'KGS' => '&#1083;&#1074;',
            'KHR' => '&#6107;',
            'KMF' => '&#67;&#70;',
            'KPW' => '&#8361;',
            'KRW' => '&#8361;',
            'KWD' => '&#1583;.&#1603;',
            'KYD' => '&#36;',
            'KZT' => '&#1083;&#1074;',
            'LAK' => '&#8365;',
            'LBP' => '&#163;',
            'LKR' => '&#8360;',
            'LRD' => '&#36;',
            'LSL' => '&#76;',
            'LTL' => '&#76;&#116;',
            'LVL' => '&#76;&#115;',
            'LYD' => '&#1604;.&#1583;',
            'MAD' => '&#1583;.&#1605;.',
            'MDL' => '&#76;',
            'MGA' => '&#65;&#114;',
            'MKD' => '&#1076;&#1077;&#1085;',
            'MMK' => '&#75;',
            'MNT' => '&#8366;',
            'MOP' => '&#77;&#79;&#80;&#36;',
            'MRO' => '&#85;&#77;',
            'MUR' => '&#8360;',
            'MVR' => '.&#1923;',
            'MWK' => '&#77;&#75;',
            'MXN' => '&#36;',
            'MYR' => '&#82;&#77;',
            'MZN' => '&#77;&#84;',
            'NAD' => '&#36;',
            'NGN' => '&#8358;',
            'NIO' => '&#67;&#36;',
            'NOK' => '&#107;&#114;',
            'NPR' => '&#8360;',
            'NZD' => '&#36;',
            'OMR' => '&#65020;',
            'PAB' => '&#66;&#47;&#46;',
            'PEN' => '&#83;&#47;&#46;',
            'PGK' => '&#75;', // ?
            'PHP' => '&#8369;',
            'PKR' => '&#8360;',
            'PLN' => '&#122;&#322;',
            'PYG' => '&#71;&#115;',
            'QAR' => '&#65020;',
            'RON' => '&#108;&#101;&#105;',
            'RSD' => '&#1044;&#1080;&#1085;&#46;',
            'RUB' => '&#1088;&#1091;&#1073;',
            'RWF' => '&#1585;.&#1587;',
            'SAR' => '&#65020;',
            'SBD' => '&#36;',
            'SCR' => '&#8360;',
            'SDG' => '&#163;', // ?
            'SEK' => '&#107;&#114;',
            'SGD' => '&#36;',
            'SHP' => '&#163;',
            'SLL' => '&#76;&#101;', // ?
            'SOS' => '&#83;',
            'SRD' => '&#36;',
            'STD' => '&#68;&#98;', // ?
            'SVC' => '&#36;',
            'SYP' => '&#163;',
            'SZL' => '&#76;', // ?
            'THB' => '&#3647;',
            'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
            'TMT' => '&#109;',
            'TND' => '&#1583;.&#1578;',
            'TOP' => '&#84;&#36;',
            'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
            'TTD' => '&#36;',
            'TWD' => '&#78;&#84;&#36;',
            'UAH' => '&#8372;',
            'UGX' => '&#85;&#83;&#104;',
            'USD' => '&#36;',
            'UYU' => '&#36;&#85;',
            'UZS' => '&#1083;&#1074;',
            'VEF' => '&#66;&#115;',
            'VND' => '&#8363;',
            'VUV' => '&#86;&#84;',
            'WST' => '&#87;&#83;&#36;',
            'XAF' => '&#70;&#67;&#70;&#65;',
            'XCD' => '&#36;',
            'XPF' => '&#70;',
            'YER' => '&#65020;',
            'ZAR' => '&#82;',
            'ZMK' => '&#90;&#75;', // ?
            'ZWL' => '&#90;&#36;',
        );

        return $currency_symbols;
        ob_end_flush();
    }
   static public function generate_timezone_list(){
    static $regions = array(
      DateTimeZone::AFRICA,
      DateTimeZone::AMERICA,
      DateTimeZone::ANTARCTICA,
      DateTimeZone::ASIA,
      DateTimeZone::ATLANTIC,
      DateTimeZone::AUSTRALIA,
      DateTimeZone::EUROPE,
      DateTimeZone::INDIAN,
      DateTimeZone::PACIFIC,
    );

    $timezones = array();
    foreach($regions as $region) {
      $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
    }

    $timezone_offsets = array();
    foreach($timezones as $timezone) {
      $tz = new DateTimeZone($timezone);
      $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    asort($timezone_offsets);

    $timezone_list = array();
    
    foreach($timezone_offsets as $timezone=>$offset){
      $offset_prefix = $offset < 0 ? '-' : '+';
      $offset_formatted = gmdate('H:i', abs($offset));
      $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
      $timezone_list[] = "(${pretty_offset}) $timezone";
    }

    return $timezone_list;
    ob_end_flush();
  }
  
  public function changesettingstatus($fields){
      $setting=Setting::find(1);
      if($setting->is_demo==0){
          if($setting->$fields=='1'){
              $setting->$fields='0';
          }else{
              $setting->$fields='1';
          }
      }
      
      Session::forget("is_rtl");
      Session::put("is_rtl",$setting->is_rtl);
      $setting->save();
      return redirect()->back();
  }
}