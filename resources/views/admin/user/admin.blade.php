@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.users')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.users')}}</li>
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
               <button  data-toggle="modal" data-target="#adduser" class="btn btn-primary btn-flat m-b-30 m-t-30" >{{__('messages.add_admin')}}</button>
               <div class="table-responsive dtdiv">
                  <table id="adminTable" class="table table-striped table-bordered dttablewidth">
                     <thead>
                        <tr>
                           <th>{{__('messages.id')}}</th>
                           <th>{{__('messages.name')}}</th>
                           <th>{{__('messages.email')}}</th>
                           <th>{{__('messages.phone')}}</th>
                           <th>{{__('messages.action')}}</th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>
      </div>
   <div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="smallmodalLabel">{{__('messages.add_admin')}}</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form action="{{url('admin/adduser')}}" method="post">
               {{csrf_field()}}
               <input type="hidden" name="user_type" value="2"/>
               <div class="modal-body">
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-12">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span></label>
                        <input id="first_name" name="first_name" type="text" class="form-control" required  placeholder="{{__('messages.first_name')}}">
                     </div>
                  </div>
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.email')}}<span class="reqfield">*</span></label>
                        <input id="email" name="email" type="text" class="form-control"  placeholder="{{__('messages.email')}}">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.phone')}}<span class="reqfield">*</span></label>
                        <input id="phone" name="phone" type="text" class="form-control"  placeholder="{{__('messages.phone')}}" required="">
                     </div>
                  </div>
                  <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.address')}}</label>
                     <textarea class="form-control" placeholder="{{__('messages.address')}}" name="address" id="address" ></textarea>
                  </div>
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.password')}}<span class="reqfield">*</span></label>
                        <input id="password" name="password" type="password" class="form-control" required placeholder="****">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.confirm_password')}}<span class="reqfield">*</span></label>
                        <input id="confirm_password" name="confirm_password" type="password" class="form-control" required placeholder="***" onchange="checkbothpwd(this.value)">
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                     @if(Session::get("is_demo")=='1')
                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary">
                                 {{__('messages.save')}}
                        </button>
                     @else
                        <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                     @endif 
               </div>
            </form>
         </div>
      </div>
   </div>
   <div class="modal fade" id="edituser" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="smallmodalLabel">
                  {{__('messages.edit_admin')}}
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form action="{{url('admin/updateuser')}}" method="post">
               {{csrf_field()}}
               <input type="hidden" name="id" id="id" />
               <div class="modal-body">
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span></label>
                        <input id="edit_first_name" name="first_name" type="text" class="form-control" required  placeholder="{{__('messages.first_name')}}">
                     </div>
                  </div>
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.email')}}<span class="reqfield">*</span></label>
                        <input id="edit_email" name="email" type="text" class="form-control" readonly placeholder="{{__('messages.email')}}">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.phone')}}<span class="reqfield">*</span></label>
                        <input id="edit_phone" name="phone" type="text" class="form-control"  placeholder="{{__('messages.phone')}}" required="">
                     </div>
                  </div>
                  <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.address')}}</label>
                     <textarea class="form-control" placeholder="{{__('messages.address')}}" name="address" id="edit_address" ></textarea>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                     @if(Session::get("is_demo")=='1')
                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary">
                                 {{__('messages.save')}}
                        </button>
                     @else
                         <button type="submit" class="btn btn-primary">{{__('messages.cancel')}}</button>
                     @endif
                
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@stop