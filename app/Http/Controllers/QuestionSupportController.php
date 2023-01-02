<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Model\QueryAns;
use App\Model\QueryTopic;
use App\Model\ContactUs;
use Image;
use Hash;
class QuestionSupportController extends Controller {
      public function __construct() {
         parent::callschedule();
    }
    public function helpindex($id){
        return view("admin.support.help")->with("page_id",$id);
    }

    public function topicdatatable($id){
    	    $support =QueryTopic::where("page_id",$id)->orderBy('id','DESC')->get();
            return DataTables::of($support)
                ->editColumn('id', function ($support) {
                   return $support->id;
                })
                ->editColumn('topic', function ($support) {
                   return $support->topic;
                }) 
                ->editColumn('action', function ($support) {
                   $del_part=url('admin/deletesupport',array('topic_id'=>$support->id));
                   $sup_part=url('admin/questionans',array('support_id'=>$support->page_id,'topic_id'=>$support->id));
                   return '<a onclick="editsupport('.$support->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editsupport"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a  href="'.$sup_part.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-code-fork f-s-25" style="margin-right: 10px;font-size: x-large;color:black"></i></a><a onclick="delete_record(' . "'" . $del_part. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';      
                })           
            ->make(true);
    }

    public function addsupporttopic(Request $request){
       $store=new QueryTopic();
       $store->topic=$request->get("topicname");
       $store->page_id=$request->get("page_id");
       $store->save();
       Session::flash('message', __('messages_error_success.topic_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }

    public function questionansindex($support_id,$topic_id){
        return view("admin.support.question")->with("support",$support_id)->with("topic",$topic_id);
    }

    public function editsupport($id){
    	$data=QueryTopic::find($id);
    	return json_encode($data);
    }

    public function deletesupport($id){
       $data=QueryTopic::find($id);
       if($data){
             $getrec=QueryAns::where("topic_id",$id)->get();
             foreach ($getrec as $k) {
                 $k->delete();
             }
          $data->delete();
          Session::flash('message', __('messages_error_success.topic_del_success'));
          Session::flash('alert-class', 'alert-success');
          return redirect()->back();
       }
           Session::flash('message', __('messages_error_success.topic_del_danger'));
           Session::flash('alert-class', 'alert-success');
            return redirect()->back();
    }

    public function updatetopic(Request $request){
    	$store=QueryTopic::find($request->get("id"));
    	$store->topic=$request->get("topicname");
    	$store->save();
    	Session::flash('message', __('messages_error_success.topic_update_success'));
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function addquesans(Request $request){
       $store=new QueryAns();
       $store->topic_id=$request->get("topic_id");
       $store->question=$request->get("ques");
       $store->answer=$request->get("ans");
       $store->save();
       Session::flash('message', __('messages_error_success.ques_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }

    public function quesdatatable($topic_id){
    	    $support =QueryAns::where("topic_id",$topic_id)->orderBy('id','DESC')->get();
            return DataTables::of($support)
                ->editColumn('id', function ($support) {
                   return $support->id;
                })
                ->editColumn('ques', function ($support) {
                   return $support->question;
                })
                ->editColumn('ans', function ($support) {
                   return $support->answer;
                }) 
                ->editColumn('action', function ($support) {
                  
                  $del_part=url('admin/deletequestion',array('topic_id'=>$support->id));
                   return '<a onclick="editques('.$support->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editques"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" .  $del_part. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';  
                })           
            ->make(true);
    }

    public function editques($id){
    	$data=QueryAns::find($id);
    	return json_encode($data);
    }

    public function getallhelp(){
        $data=array();
        $gettext=QueryTopic::with("Question")->where("page_id",'1')->get();
        foreach ($gettext as $k) {
             $data[]=$k->topic;
             foreach ($k->Question as $k1) {
                 $data[]=$k1->question;
             }
        }
      
            return json_encode(array_values(array_unique($data)));
    }

    public function updatequestion(Request $request){
       $store=QueryAns::find($request->get("id"));
       $store->question=$request->get("ques");
       $store->answer=$request->get("ans");
       $store->save();
       Session::flash('message', __('messages_error_success.ques_update_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }

    public function deletequestion($id){
    	$store=QueryAns::find($id);
    	$store->delete();
    	Session::flash('message', __('messages_error_success.ques_del_success'));
      Session::flash('alert-class', 'alert-success');
      return redirect()->back();
    }

    public function contactindex(){
       return view("admin.contact");
    }

    public function contactdatatable(){
        $contact =ContactUs::orderBy('id','DESC')->get();
            return DataTables::of($contact)
                ->editColumn('id', function ($contact) {
                   return $contact->id;
                })
                ->editColumn('name', function ($contact) {
                   return $contact->name;
                }) 
                ->editColumn('email', function ($contact) {
                   return $contact->email;
                }) 
                ->editColumn('phone', function ($contact) {
                   return $contact->phone;
                }) 
                ->editColumn('subject', function ($contact) {
                   return $contact->subject;
                }) 
                ->editColumn('message', function ($contact) {
                   return $contact->message;
                }) 
                ->editColumn('action', function ($contact) {
                   $del_con=url('admin/deletecontact',array('id'=>$contact->id));
                   return '<a onclick="delete_record(' . "'" . $del_con. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';      
                })           
            ->make(true);
    }

    public function deletecontact($id){
        $store=ContactUs::find($id);
        $store->delete();
        Session::flash('message', __('messages_error_success.contact_del'));
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }
}