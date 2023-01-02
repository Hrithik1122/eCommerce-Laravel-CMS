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
use App\Model\Brand;
use Image;
use App\Model\Sepicalcategories;
use Hash;
class Categorycontroller extends Controller {
     public function __construct() {
         parent::callschedule();
    }
    public function index(){  
         $category=Categories::where("is_delete",'0')->get();
         $brand=Brand::where("is_delete",'0')->get();             
        return view('admin.categories.category')->with("category",$category)->with("brand",$brand);
    }

    public function getallsubcategory(){
         $data=Categories::where("parent_category","==",0)->where("is_delete",'0')->get();
         return json_encode($data);
    }

    public function categorydatatable(){
         $category =Categories::orderBy('id','DESC')->where("is_delete",'0')->where('parent_category','0')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('name', function ($category) {
                return $category->name;
            })
            ->editColumn('image', function ($category) {
                return asset("public/upload/category/image").'/'.$category->image ;
            })
            ->editColumn('action', function ($category) {
                 $subcategory=url('admin/subcategory',array('id'=>$category->id));
                 $deleteuser=url('admin/deletecategory',array('id'=>$category->id));
                 $return = '<a onclick="editcategory('.$category->id.')"   rel="tooltip" class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editcategory"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a  href="'.$subcategory.'" rel="tooltip" title="Sub Category" class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-code-fork f-s-25" style="margin-right: 10px;font-size: x-large;color:black"></i></a>';
                 return $return;              
            })           
            ->make(true);
    } 

    public function addcategory(Request $request){
         if ($files = $request->file('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/category/image/';
                $picture = str_random(10).time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('image')->move($destinationPath, $picture);
                $img_url =$picture;
            }
            else{
                    $img_url="";
            }
       $store=new Categories();
       $store->name=$request->get("category_name");
       $store->image=$img_url;
       $store->save();
       Session::flash('message',__('messages_error_success.category_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/category");
    }

    public function getcategorybyid($id){
       $data=Categories::find($id);
       return $data;
    }

    public function addsepicalcategory(){
        $category=Categories::where("parent_category",0)->where("is_delete",'0')->get();
        return view("admin.sepical.add")->with("category",$category);
    }

    public function storesepicalcategory(Request $request){
         if ($files = $request->file('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/category/image/';
                $picture = str_random(10).time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('image')->move($destinationPath, $picture);
                $img_url =$picture;
            }
            else{
                    $img_url="";
            }
        $store=new Sepicalcategories();
        $store->title=$request->get("title");
        $store->description=$request->get("description");
        $store->category_id=$request->get("category");
        $store->image=$img_url;
        $store->save();
        Session::flash('message',__('messages_error_success.sepcategory_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/sepical_category");
    }

    public function updatecategory(Request $request){
         if ($files = $request->file('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/category/image/';
                $picture = str_random(10).time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('image')->move($destinationPath, $picture);
                $img_url =$picture;
            }
            else{
                    $img_url="";
            }
        $store=Categories::find($request->get("id"));
       $store->name=$request->get("category_name");
       $store->image=$img_url;
       $store->save();
       Session::flash('message',__('messages_error_success.category_update_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }

    public function subindex($id){
       $data=Categories::find($id);
       return view("admin.categories.subcategory")->with("parent_id",$id)->with("parent_name",$data->name);
    }

    public function subdatatable($id){
      $category =Categories::orderBy('id','DESC')->where("is_delete",'0')->where('parent_category',$id)->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('name', function ($category) {
                return $category->name;
            })           
            ->editColumn('action', function ($category) { 
                 $brand=url('admin/brand',array('id'=>$category->id));
                 $deletesub=url('admin/deletecategory',array('id'=>$category->id));
                 $return = '<a onclick="editcategory('.$category->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editsubcategory"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deletesub. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a  href="'.$brand.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-code-fork f-s-25" style="font-size: x-large;color:black"></i></a>';
                 return $return;              
            })           
            ->make(true);
    }

    public function subaddcategory(Request $request){
       $store=new Categories();
       $store->name=$request->get("name");
       $store->parent_category=$request->get("parentid");
       $store->save();
       Session::flash('message',__('messages_error_success.subcat_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/subcategory/".$store->parent_category);
    }

    public function brandindex($id){
        $data=Categories::find($id);
        $parent=Categories::find($data->parent_category);
        $parent_name=$parent->name;
        $subcategory=$data->name;
        return view("admin.categories.brand")->with("subcategoryid",$id)->with("parent_name",$parent_name)->with("subcategory",$subcategory)->with("parent_ids",$parent->id);
    }

    public function branddatatable($id){
       $category =Brand::orderBy('id','DESC')->where("is_delete",'0')->where('category_id',$id)->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('name', function ($category) {
                return $category->brand_name;
            })           
            ->editColumn('action', function ($category) { 
                 $brand=url('admin/brand',array('id'=>$category->id));
                 $del_brand=url('admin/deletebrand',array('id'=>$category->id));

                 $return = '<a onclick="editbrand('.$category->id.')"  rel="tooltip" class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editbrand"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $del_brand. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';
                 return $return;              
            })           
            ->make(true);
    }

    public function addbrand(Request $request){
         $store=new Brand();
         $store->brand_name=$request->get("name");
         $store->category_id=$request->get("category_id");
         $store->save();
         Session::flash('message',__('messages_error_success.brand_add_success')); 
         Session::flash('alert-class', 'alert-success');
         return redirect("admin/brand/".$store->category_id);
    }

    public function getbrandbyname($id){
       $data=Brand::find($id);
       return $data->brand_name;
    }

    public function updatebrand(Request $request){
         $store=Brand::find($request->get("id"));
         $store->brand_name=$request->get("category_name");
         $store->save();
         Session::flash('message',__('messages_error_success.brand_update_success')); 
         Session::flash('alert-class', 'alert-success');
         return redirect("admin/brand/".$store->category_id);
    }

    public function viewcategory(){
       $category=Categories::all();
       $brand=Brand::all();
       return view("admin.Categories.viewcategory")->with("category",$category)->with("brand",$brand);
    }

    public function sepical_category(){
        return view("admin.sepical.default");
    }

    public function sepicalcategorytable(){
        $category =Sepicalcategories::orderBy('id','DESC')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('image', function ($category) {
                return asset('public/upload/category/image')."/".$category->image;
            }) 
            ->editColumn('title', function ($category) {
                return $category->title;
            })
            ->editColumn('category', function ($category) {
                $data=Categories::find($category->category_id);
                return  $data->name;
            })
            ->editColumn('description', function ($category) {
                return $category->description;
            })           
            ->editColumn('action', function ($category) { 
             
                $editoption=url('admin/editsepicalcategory',array('id'=>$category->id)); 
                $editchange=url('admin/sepicalchange',array('id'=>$category->id));
                if($category->is_active=='1'){
                    $color="green";
                }   
                else{
                    $color="red";
                }       
                 return '<a href="'.$editoption.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a><a href="'.$editchange.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a>';              
            })           
            ->make(true);
    }

    public function editsepicalcategory($id){
        $data=Sepicalcategories::find($id);
        $category=Categories::where("parent_category",0)->where("is_delete",'0')->get();
        return view("admin.sepical.edit")->with("data",$data)->with("category",$category);
    }

    public function updatesepicalcategory(Request $request){
         if ($files = $request->file('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/category/image/';
                $picture = str_random(10).time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('image')->move($destinationPath, $picture);
                $img_url =$picture;
            }
            else{
                    $img_url=$request->get("real_image");
            }
        $store=Sepicalcategories::find($request->get("id"));
        $img=$store->image;
        $store->title=$request->get("title");
        $store->description=$request->get("description");
        $store->category_id=$request->get("category");
        $store->image=$img_url;
        $store->save();
         if($img!=$img_url){
            $image_path="";
            if($img!=""){
                $image_path = public_path() ."/upload/category/image/".$img;
            }
            if(file_exists($image_path)) {
                unlink($image_path);
            }
        }
        Session::flash('message',__('messages_error_success.sepcategory_update_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/sepical_category");
    }

    public function sepicalchange($id){
        $data=Sepicalcategories::all();
        foreach ($data as $ke) {
           $ke->is_active='0';
           $ke->save();
        }
        $store=Sepicalcategories::find($id);
        $store->is_active='1';
        $store->save();
         Session::flash('message',__('messages_error_success.sepcategory_change_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/sepical_category");
    }

    public function deletecategory($id){
        $store=Categories::find($id);
        $store->is_delete='1';
        $store->save();
        $product=Product::orwhere('category',$id)->orwhere("subcategory",$id)->get();
        foreach ($product as $k) {
            $da=Product::where("id",$k->id)->update(["is_deleted"=>'1']);
        }
        Session::flash('message',__('messages_error_success.category_del')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function deletebrand($id){
       $store=Brand::find($id);
       $store->is_delete='1';
       $store->save();
       $product=Product::where('brand',$id)->get();
        foreach ($product as $k) {
            $da=Product::where("id",$k->id)->update(["is_deleted"=>'1']);
        }
       Session::flash('message',__('messages_error_success.brand_del')); 
       Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

}
