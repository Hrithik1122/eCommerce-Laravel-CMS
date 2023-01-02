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
use App\Model\AttributeSet;
use App\Model\Options;
use App\Model\Optionvalues;
use App\Model\Product;
use App\Model\Review;
use App\Model\ProductAttributes;
use App\Model\ProductOption;
use App\Model\Taxes;
use Image;
use Artisan;
use Hash;
class ProductController extends Controller {
     public function __construct() {
         parent::callschedule();
    }
     public function showproduct(){
        return view("admin.product.product");
     }

     public function showattset(){
        return view("admin.product.attributeset");
     }

     public function checktotalproduct(){
          $category =Product::orderBy('id','DESC')->where('is_deleted','0')->where("status",'1')->get();
          if(count($category)>1){
              return 0;
          }else{
              return 1;
          }
     }

     public function getoptionvalues($id){
         $optionvalues=Options::with("optionlist")->where("id",$id)->first();
         return json_encode($optionvalues);
     }

     public function getallproduct(){
            $data=Product::all();
            return json_encode($data);
     }
     

     public function showaddcatalog($id,$tab){

          $data=array();
          $subcategory=array();
          $brand=array();
          $optionls=array();
          if($id!=0){
                $data=Product::find($id);
                if($data->category){
                    $subcategory=Categories::where("parent_category",$data->category)->where("is_delete",'0')->get();
                }
                if($data->subcategory){
                    $brand=Brand::where("category_id",$data->subcategory)->where("is_delete",'0')->get();
                }
                $data->optionls=ProductOption::where("product_id",$id)->first();
                $data->attributels=ProductAttributes::where("product_id",$id)->get();

                
          }
          $optionvalues=Options::with("optionlist")->where("is_deleted",'0')->get();
         
          $category=Categories::where("parent_category",0)->where("is_delete",'0')->get(); 
          $tax=Taxes::all();         
          return view("admin.product.addproduct")->with("category",$category)->with("product_id",$id)->with("tab",$tab)->with("taxes",$tax)->with("data",$data)->with("subcategory",$subcategory)->with("brand",$brand)->with("optionvalues",$optionvalues);
     }

     public function getallsearchproduct(Request $request){
         if($request->get("id")==0){
              $data=Product::all();
              return json_encode($data);
         }
         else{
             $data=Product::where("category",$request->get("id"))->get();
              return json_encode($data);
         }
     }
     public function getproductprice($id){
        $data=Product::find($id);
        return json_encode($data);
     }

     public function productdatatable(){
         $category =Product::orderBy('id','DESC')->where('is_deleted','0')->where("status",'1')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('thumbnail', function ($category) {
                return asset('public/upload/product')."/".$category->basic_image;
            })
              ->editColumn('name', function ($category) {
                return $category->name;
            })
            ->editColumn('price', function ($category) {
                return $category->price;
            })           
            ->editColumn('action', function ($category) {                 
                  $editoption=url('admin/savecatalog',array('id'=>$category->id,'tab'=>'1')); 
                  $changestaus=url('admin/changeproductstatus',array('id'=>$category->id)); 
                  $deletecatlog=url('admin/deletecatlog',array('id'=>$category->id)); 
                   if($category->status=='1'){
                        $color="green";
                    }   
                    else{
                        $color="red";
                    }              
                 $return = '<a  href="'.$editoption.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deletecatlog. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a href="'.$changestaus.'" rel="tooltip" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a>';
                 return $return;              
            })           
            ->make(true);
     }

     public function changeproductstatus($id){        
        if(Session::get('is_demo')=='1'){
            Session::flash('message','This function is currently disable as it is only a demo website, in your admin it will work perfect');
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
        $store=Product::find($id);
        if($store->status=='0'){
            $store->status='1';
        }
        else{
            $store->status='0';
        }
        $store->save();
        Session::flash('message',__('messages_error_success.product_status_update')); 
        Session::flash('alert-class', 'alert-success');
       return redirect()->back();
     }
      public function editproduct($id){
        $product=Product::find($id);
        $category=Categories::where("parent_category",0)->where("is_delete",'0')->get();
        $subcategory=Categories::where("parent_category",$product->category)->where("is_delete",'0')->get();
        $brand=Brand::where("category_id",$product->subcategory)->where("is_delete",'0')->get();
        $attribute=ProductAttributes::where("product_id",$id)->get();
        $optionvalue=ProductOption::where("product_id",$id)->first();
        $attributedrop=AttributeSet::whereHas('attributelist', function($q)use($product) {$q->where("is_delete",'0')->where("category",$product->category);})->where("is_deleted",'0')->get();
       foreach ($attributedrop as $k) {
            $getdata=Attributes::where("att_set_id",$k->id)->where("is_delete",'0')->where("category",$product->category)->get();
            $k->attributelist=$getdata;
        } 
        $optionvalues=Options::with("optionlist")->get();
        $tax=Taxes::all();
        return view("admin.product.edit.default")->with("product",$product)->with("product_attribute",$attribute)->with("product_option",$optionvalue)->with("attributedrop",$attributedrop)->with("optionvalues",$optionvalues)->with("category",$category)->with("subcategory",$subcategory)->with("brand",$brand)->with("tax",$tax);
     }
  
     public function productlist($id,$pro_id){
         if($pro_id==0){
             $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("status",'1')->get();
         }
         else{
             $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("status",'1')->where("subcategory",$id)->where("id","!=",$pro_id)->get();
         }
         
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('thumbnail', function ($category) {
                return asset('public/upload/product')."/".$category->basic_image;
            })
              ->editColumn('name', function ($category) {
                return $category->name;
            })
            ->editColumn('price', function ($category) {
                return $category->price;
            })           
                       
            ->make(true);
     }

    
    

     public function getsubcategory($id){
        $data=Categories::where("parent_category",$id)->where("is_delete",'0')->get();
        return json_encode($data);
     }

   public function saveproduct(Request $request){
        if($request->get("product_id")!=0){
            $store=Product::find($request->get("product_id"));
        }
        else{
            $store=new Product();
        }        
        $store->name=$request->get("pro_name");
        $store->description=$request->get("description");
        $store->category=$request->get("category");
        $store->subcategory=$request->get("subcategory");
        $store->brand=$request->get("brand");
        $store->tax_class=$request->get("texable");
        $store->status='1';
        $store->product_color=$request->get("colorpro");
        $store->color_name=$request->get("colorname");
        $store->meta_keyword=$request->get("metakeyword"); 
        $store->save();
        return redirect('admin/savecatalog/'.$store->id.'/2');
     }
     
    
     public function saveprice(Request $request){
        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/2');
        }
        if($request->get("mrp")<$request->get("price")){ 
            Session::flash('message',__('messages_error_success.selling_mrp_vaildate')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/'.$request->get("product_id").'/2');
        }
        else{
            if($request->get("special_price")!=""){
                 if($request->get("price")<$request->get("special_price")){
                     Session::flash('message',__('messages_error_success.check_price')); 
                     Session::flash('alert-class', 'alert-danger');
                     return redirect('admin/savecatalog/'.$request->get("product_id").'/2');
                 }
                 if($request->get("spe_pri_start")==""&&$request->get("spe_pri_to")==""){
                     Session::flash('message',__('messages_error_success.sepical_price_vaildate')); 
                     Session::flash('alert-class', 'alert-danger');
                     return redirect('admin/savecatalog/'.$request->get("product_id").'/2');
                 }
            }
        }
        $store=Product::find($request->get("product_id"));
        $store->price=number_format((float)$request->get("price"), 2, '.', '');
        $store->selling_price=number_format((float)$request->get("price"), 2, '.', '');
        $store->MRP=number_format((float)$request->get("mrp"), 2, '.', '');
        
                $store->special_price=number_format((float)$request->get("special_price"), 2, '.', '');
                $store->special_price_start=$request->get("spe_pri_start");
                $store->special_price_to=$request->get("spe_pri_to");
        
        $store->save();
         parent::productupdate();
        return redirect('admin/savecatalog/'.$store->id.'/3');
     }

     public function saveinventory(Request $request){
        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/3');
        }

        if($request->get("sku")==""){
            $store=Product::find($request->get("product_id"));
                $store->sku=$request->get("sku");
                $store->inventory=$request->get("inventory");
                $store->stock=$request->get("stock");
                $store->save();
                 return redirect('admin/savecatalog/0/3');
        }else{
            $checksku=Product::where("sku",$request->get("sku"))->where("id","!=",$request->get("product_id"))->first();
              if(!isset($checksku)){
                $store=Product::find($request->get("product_id"));
                $store->sku=$request->get("sku");
                $store->inventory=$request->get("inventory");
                $store->stock=$request->get("stock");
                $store->save();
                return redirect('admin/savecatalog/'.$store->id.'/4');
             }
             Session::flash('message',__('messages_error_success.sku_already')); 
             Session::flash('alert-class', 'alert-danger');
             return redirect('admin/savecatalog/'.$request->get("product_id").'/3'); 
         }
              
     }

     public function saveproductimage(Request $request){
         if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/4');
        }
         $add_img=array();
        $store=Product::find($request->get("product_id"));
        $adddata=explode(",",$store->additional_image);
        if($request->get("basic_img")!=""){
            if(strstr($request->get("basic_img"),"http")==""){
                $data = $request->get("basic_img");
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $folderName = '/upload/product/';
                $destinationPath = public_path() . $folderName;
                $file_name=uniqid() . '.png';
                $file = $destinationPath .$file_name;
                $data = base64_decode($data);
                file_put_contents($file, $data);              
                $store->basic_image=$file_name;
                if($request->get("real_basic_img")!=$file_name){
                        $image_path="";
                        if($request->get("real_basic_img")!=""){
                            $image_path = public_path() ."/upload/product/".$request->get("real_basic_img");
                        }
                        if(file_exists($image_path)) {
                            try{
                                 unlink($image_path);
                            }
                            catch(\Exception $e)
                            {
                                
                            }
                            
                        }
                }
            }
                       
        }
        if($request->get("additional_img")!=""){
             $add_img=array();
             $data=$request->get("additional_img");
            
             foreach (array_filter($data) as $k) {
                if(strstr($k,"http")==""){  
                        $data1 =$k;                 
                        list($type, $data1) = explode(';', $data1);
                        list(, $data1)      = explode(',', $data1);
                        $folderName = '/upload/product/';
                        $destinationPath = public_path() . $folderName;
                        $file_name=uniqid() . '.png';
                        $file = $destinationPath .$file_name;
                        $data = base64_decode($data1);
                        file_put_contents($file, $data);
                        $add_img[]=$file_name;                        
                }  
                else{
                        $arr=explode("/",$k);
                        $add_img[]=$arr[count($arr)-1];
                }            
             }
             if(!empty(array_filter($add_img))){
                 $store->additional_image=implode(',',$add_img);
             }
              
        }
             
        $store->save();
        if(!empty($adddata)){
            foreach ($adddata as $k) {
                if(!in_array($k,$add_img)){
                    $image_path = public_path() ."/upload/product/".$k;
                    if(file_exists($image_path)) {
                        try{
                                 unlink($image_path);
                            }
                            catch(\Exception $e)
                            {
                                
                            }
                    }
                }
            }
        }
        
        return redirect('admin/savecatalog/'.$store->id.'/5');
     }

   
     
     public function saveproductattibute(Request $request){
       // dd($request->all());exit;
        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/5');
        }
        $arr=array_values($request->get("attributeset"));
        if(count($arr)==0){             
             return redirect('admin/savecatalog/'.$request->get("product_id").'/6');
        }
        $checkproattri=ProductAttributes::where("product_id",$request->get("product_id"))->delete();
        
         for ($i=0; $i <count($arr); $i++) {            
                     $store=new ProductAttributes();
                     $store->product_id=$request->get("product_id");
                     $store->attributeset=$arr[$i]['set'];
                     $store->attribute=implode(",",$arr[$i]['label']);
                     $store->value=implode(",", $arr[$i]['value']);
                     $store->save();
                       
         }
         return redirect('admin/savecatalog/'.$request->get("product_id").'/6');
     }

     public function saveproductoption(Request $request){
        
         if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/6');
        }
        if($request->get("totaloption")==0){
            return redirect('admin/savecatalog/'.$request->get("product_id").'/7');
        }
        $arr=array_values($request->get("options"));
        
        $name=array();
        $type=array();
        $required=array();
        $label=array();
        $price=array();
         for ($i=0; $i <count($arr); $i++) {  
             if(isset($arr[$i]['name'])&&isset($arr[$i]['type'])&&isset($arr[$i]['required'])&&isset($arr[$i]['label'])&&isset($arr[$i]['price'])){
                 $name[]=$arr[$i]['name'];
                    $type[]=$arr[$i]['type'];
                    $required[]=$arr[$i]['required'];
                    $label[]=implode(",", $arr[$i]['label']);
                    $price[]=implode(",", $arr[$i]['price']);
             }
                
         }   
         $checkoption=ProductOption::where("product_id",$request->get("product_id"))->delete();
         $store=new ProductOption();    
         $store->product_id=$request->get("product_id");
         $store->name=implode(",",$name);
         $store->type=implode(",",$type);
         $store->is_required=implode(",",$required);
         $store->label=implode("#",$label);
         $store->price=implode("#",$price);
         $store->save();
          return redirect('admin/savecatalog/'.$request->get("product_id").'/7');
        
     }
    

  public function saverealtedprice(Request $request){
   
     if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/7');
        }
        if($request->get("totaloption")==0){
            Session::flash('message',__('messages_error_success.pro_add')); 
            Session::flash('alert-class', 'alert-success');
            return redirect('admin/product');
        }
        $store=Product::find($request->get("product_id"));
        $store->related_product=implode(",",$request->get('related_id'));
        $store->save();
        
          Session::flash('message',__('messages_error_success.pro_add')); 
          Session::flash('alert-class', 'alert-success');
          return redirect('admin/product');
  }
     public function getattibutevalue($id){
        $data=Attributevalues::where("att_id",$id)->get();
        return json_encode($data);
     }


     public function getbrandbyid($id){
        $data=Brand::where("category_id",$id)->get();
        return json_encode($data);
     }
  
     public function deletecatlog($id){
        $data=Product::find($id);
        $data->is_deleted='1';
        $data->save();
        Session::flash('message',__('messages_error_success.catalog_del')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
     }
     
   
     public function indexoption(){
        return view("admin.product.options");
     }

     public function Optiondatatable(){
       $option =Options::orderBy('id','DESC')->where("is_deleted",'0')->get();
         return DataTables::of($option)
            ->editColumn('id', function ($option) {
                return $option->id;
            })
            ->editColumn('name', function ($option) {
                return $option->name;
            })
            ->editColumn('type', function ($option) {
                if($option->type==1){
                    $status=__('messages.dropdown');
                }
                else if($option->type==2){
                    $status=__('messages.checkbox');
                }
                else if($option->type==3){
                    $status=__('messages.radiobutton');
                }else{
                    $status=__('messages.multiple_select');
                }
                return $status;
            })            
            ->editColumn('action', function ($option) {   
                 $editoption=url('admin/editoption',array('id'=>$option->id)); 
                 $deloption=url('admin/deleteoption',array('id'=>$option->id));              
                 $return = '<a  href="'.$editoption.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deloption. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';
                 return $return;              
            })           
            ->make(true);
     }

     public function showaddoption(){
        return view("admin.product.addoptionvalues");
     }

     public function saveoption(Request $request){
          $label=$request->get('label');
          $price=$request->get('price');      
          $store=new Options();
          $store->name=$request->get("option_name");
          $store->type=$request->get("option_type");
          $store->is_required=$request->get("option_required");
          $store->save();
         
          for($i=0;$i<count($request->get('label'));$i++){
              $add=new Optionvalues();
              $add->option_id=$store->id;
              $add->label=$label[$i];
              $add->price=$price[$i];
              $add->save();
          }
          Session::flash('message',__('messages_error_success.option_add_success')); 
          Session::flash('alert-class', 'alert-success');
          return redirect('admin/options');

     }

     public function editoption($id){
        $option=Options::find($id);
        $optionvalue=Optionvalues::where("option_id",$id)->get();
        return view("admin.product.editoption")->with("option",$option)->with("optionvalue",$optionvalue);
     }

     public function updateoption(Request $request){
          $label=$request->get('label');
          $price=$request->get('price');       
          $store=Options::find($request->get("option_id"));
          $store->name=$request->get("option_name");
          $store->type=$request->get("option_type");
          $store->is_required=$request->get("option_required");
          $store->save();
          $delrecord=Optionvalues::where("option_id",$request->get("option_id"))->delete();
          for($i=0;$i<count($request->get('label'));$i++){
              $add=new Optionvalues();
              $add->option_id=$request->get("option_id");
              $add->label=$label[$i];
              $add->price=$price[$i];
              $add->save();
          }
          Session::flash('message',__('messages_error_success.option_update_success')); 
          Session::flash('alert-class', 'alert-success');
          return redirect('admin/options');
     }

     //attribute

     

     public function showreview(){
         return view("admin.product.review");
     }

     public function reviewdatatable($id){

        $review=array();
        if($id=="0"){
            $review =Review::with('product','userdata')->orderBy('id','DESC')->get();
        }
        else{
            $review =Review::with('product','userdata')->where("product_id",$id)->orderBy('id','DESC')->get();
        }
         
         return DataTables::of($review)
            ->editColumn('id', function ($review) {
                return $review->id;
            })
            ->editColumn('pro_name', function ($review) {
                return $review->product->name;
            })
            ->editColumn('rev_name', function ($review) {
               if($review->userdata!=""){
                     return $review->userdata->first_name;
                }
                else{
                    return "";
                }
            })
             ->editColumn('rating', function ($review) {
                return $review->ratting.'/5';
            })
            ->editColumn('review', function ($review) {
                return $review->review;
            })   
            ->editColumn('action', function ($attribute) { 
                 
                 $deletereview=url('admin/deletereview',array('id'=>$attribute->id));
                 $editoption=url('admin/changereview',array('id'=>$attribute->id));
                 $return = '<a onclick="delete_record(' . "'" . $deletereview. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';
                 return $return;              
            })           
            ->make(true);
     }

     public function changereview($id){
        $store=Review::find($id);
        if($store->is_approved=='1'){
            $store->is_approved='0';
        }
        else{
            $store->is_approved='1';
        }
        $store->save();
         Session::flash('message',__('messages_error_success.review_status_change')); 
         Session::flash('alert-class', 'alert-success');
         return redirect()->back();
     }

     public function deleteoption($id){
         $data=Options::find($id);
         $data->is_deleted='1';
         $data->save();
         Session::flash('message',__('messages_error_success.option_delete')); 
         Session::flash('alert-class', 'alert-success');
         return redirect('admin/options');
     }

   
     public function deletereview($id){
        $data=Review::find($id);
        $data->delete();
        Session::flash('message',__('messages_error_success.review_del_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
     }
}