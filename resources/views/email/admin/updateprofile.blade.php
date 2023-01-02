@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.edit_profile')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.edit_profile')}}</li>
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
               <strong class="card-title">{{__('messages.edit_profile')}}</strong>
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
                     <form action="{{url('admin/updateprofile')}}" method="post" novalidate="novalidate" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.first_name')}}
                           <span class="florig">*</span>
                           </label>
                           <input type="text" id="name" placeholder="{{__('messages.first_name')}}" class="form-control" name="name" value="{{$data->first_name}}">
                        </div>
                        <div class="form-group">
                           <label for="lname" class=" form-control-label">
                           {{__('messages.last_name')}}
                           <span class="florig">*</span>
                           </label>
                           <input type="text" id="lname" name="lname" placeholder="{{__('messages.last_name')}}" class="form-control" value="{{$data->last_name}}">
                        </div>
                        <div class="form-group">
                           <label for="email" class=" form-control-label">
                           {{__('messages.email')}}
                           </label>
                           <input type="text" readonly id="email" name="email" placeholder="{{__('messages.email')}}" class="form-control" value="{{$data->email}}">
                        </div>
                        <div class="form-group">
                           <label for="file" class=" form-control-label">  
                           {{__('messages.profile_picture')}}
                           </label>
                           <img src="{{asset('public/upload/profile/'.'/'.$data->profile_pic)}}" class="imgsize1" />
                           <div>
                              <input type="file" id="file" name="file" class="form-control-file">
                           </div>
                        </div>
                        <div>
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