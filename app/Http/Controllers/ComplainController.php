<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\User;
use App\Model\Complain;
use App\Model\Product;
use Hash;
use Auth;
class ComplainController extends Controller {

   public function index(){
      return view('admin.complain');
   }

   public function complaindatatable(){
         $category =Complain::orderBy('id','DESC')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('email', function ($category) {
                return $category->email;
            }) 
             ->editColumn('description', function ($category) {
                return $category->description;
            })
            ->editColumn('complain_type', function ($category) {
                return $category->report_error;
            })           
            ->editColumn('action', function ($category) {
                 if(Session::get("is_demo")=='1')
                 {
                      return '<a  onclick="demofun()" rel="tooltip"  target="blank" class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-envelope f-s-25" style="margin-right: 10px;font-size: x-large;color:black"></i></a>';  
                 }
                 else{
                        return '<a  href="https://mail.google.com/mail/?view=cm&fs=1&to='.$category->email.'&su=Complain '.$category->report_error.'&body='.$category->description.'" rel="tooltip"  target="blank" class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-envelope f-s-25" style="margin-right: 10px;font-size: x-large;color:black"></i></a>';  
                 }
                           
            })           
            ->make(true);
   }

   public function datatabletest(){
     $data=Product::orderBy('id','DESC')->paginate(10);
     return view("admin.datatable")->with("data",$data);
   }

}