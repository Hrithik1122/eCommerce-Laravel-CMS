@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
         	@if($id==1)
            <h1>{{__('messages.Android Server Key')}}</h1>
            @endif
            @if($id==2)
            <h1>{{__('messages.Iphone Server Key')}}</h1>
            @endif
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
            	@if($id==1)
		            <li class="active">{{__('messages.Android Server Key')}}</li>
		        @endif
		        @if($id==2)
		            <li class="active">{{__('messages.Iphone Server Key')}}</li>
		        @endif             
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="rowset">
      <div class="col-md-9">
         <div class="card">
            <div class="card-header">
            	@if($id==1)
		            <strong class="card-title">{{__('messages.Android Server Key')}}</strong>
		        @endif
		        @if($id==2)
		            <strong class="card-title">{{__('messages.Iphone Server Key')}}</strong>
		        @endif 
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
                     <form action="{{url('admin/updatekey')}}" method="post">
                        {{csrf_field()}}
                       
                        <input type="hidden" id="id" name="id" value="{{$id}}">
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                             @if($id==1)
					                 {{__('messages.Android Server Key')}}
      					        @endif
      					        @if($id==2)
      					            {{__('messages.Iphone Server Key')}}
      					        @endif 
                           <span class="reqfield">*</span>
                           </label>
                           <textarea class="form-control" name="serverkey" id="serverkey" placeholder="" required="">{{$serverkey}}</textarea>
                        </div>
                        <div>
                               @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                               {{__('messages.save')}}
                                        </button>
                                     @else
                                           <button class="btn btn-primary florig" type="submit"> {{__('messages.update')}}</button>
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