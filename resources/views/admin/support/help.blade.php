@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            @if($page_id==1)
            <h1>{{__('messages.helpsupport')}}</h1>
            @endif
            @if($page_id==2)
            <h1>{{__('messages.termscon')}}</h1>
            @endif
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               @if($page_id==1)
               <li class="active">{{__('messages.helpsupport')}}</li>
               @endif
               @if($page_id==2)
               <li class="active">{{__('messages.termscon')}}</li>
               @endif
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            @if(Session::has('message'))
            <div class="col-sm-12">
               <div class="alert  {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">{{ Session::get('message') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
            </div>
            @endif
            <button  data-toggle="modal" data-target="#adduser" class="btn btn-primary btn-flat m-b-30 m-t-30" >{{__('messages.add')}} {{__('messages.topic')}}</button>
            <div class="table-responsive dtdiv">
               <table id="QuestionTable" class="table table-striped table-bordered dttablewidth">
                  <thead>
                     <tr>
                        <th>{{__('messages.id')}}</th>
                        <th>{{__('messages.topic')}}</th>
                        <th>{{__('messages.action')}}</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
</div>
<div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="smallmodalLabel">{{__('messages.add')}} {{__('messages.topic')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{url('admin/addsupporttopic')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="page_id" id="page_id" value="{{$page_id}}"/>
            <div class="modal-body">
               <div class="form-group col-md-12 paddiv" >
                  <label for="cc-payment" class="control-label mb-1">{{__('messages.topicname')}}<span class="reqfield">*</span></label>
                  <input id="topicname" name="topicname" type="text" class="form-control" required  placeholder="{{__('messages.topicname')}}">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                                    @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.submit')}}
                                        </button>
                                     @else
                                             <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                                     @endif 
             
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal fade" id="editsupport" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="smallmodalLabel">
               {{__('messages.edit')}} {{__('messages.topic')}}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{url('admin/updatetopic')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="id" id="edit_id" />
            <div class="modal-body">
               <div class="form-group col-md-12 paddiv">
                  <label for="cc-payment" class="control-label mb-1">{{__('messages.topicname')}}<span class="reqfield">*</span></label>
                  <input id="edit_topicname" name="topicname" type="text" class="form-control" required  placeholder="{{__('messages.topicname')}}">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
               @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.submit')}}
                                        </button>
                                     @else
                                            <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                                     @endif 
              
            </div>
         </form>
      </div>
   </div>
</div>
@stop