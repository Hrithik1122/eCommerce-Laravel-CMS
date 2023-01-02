@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.change_pwd')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.change_pwd')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="row rowset">
      <div class="col-lg-6">
         <div class="card">
            <div class="card-header">
               <strong class="card-title">{{__('messages.change_pwd')}}</strong>
            </div>
            <div class="card-body">
               <div id="pay-invoice">
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
                     <form action="{{url('admin/updatepassword')}}" method="post"  novalidate="novalidate" enctype="multipart/form-data">
                        {{csrf_field()}}                                           
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.ent_current_pwd')}}
                           <span class="reqfield">*</span>
                           </label>
                           <input type="password" id="cpwd" placeholder="{{__('messages.ent_current_pwd')}}" class="form-control" name="cpwd" required="" onchange="checkcurrentpwd(this.value)">
                        </div>
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.ent_new_pwd')}}
                           <span class="reqfield">*</span>
                           </label>
                           <input type="password" id="npwd" placeholder="{{__('messages.ent_new_pwd')}}" class="form-control" name="npwd" required="" >
                        </div>
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.re_enter_pwd_en')}}
                           <span class="reqfield">*</span>
                           </label>
                           <input type="password" id="rpwd" placeholder="{{__('messages.re_enter_pwd_en')}}" class="form-control" name="rpwd" onchange="changecheckboth(this.value)" required="">
                        </div>
                        <div class="form-group">
                           
                            @if(Session::get("is_demo")=='1')
                                 <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-lg btn-info btn-block">
                                    {{__('messages.update')}}
                                </button>
                                @else
                                  <button id="payment-button" type="submit" class="btn btn-lg btn-info btn-block">
                           {{__('messages.update')}}
                           </button>
                                @endif
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@stop