@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
         	 <h1>{{__('messages.news')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
                <li class="active"> <h1>{{__('messages.news')}}</h1></li>           
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
             <strong class="card-title">{{__('messages.news')}}</strong>
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
                     <form action="{{url('admin/sennews')}}" method="post">
                        {{csrf_field()}}
                       
                       
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                            {{__('messages.news')}}
                           <span class="reqfield">*</span>
                           </label>
                           <textarea class="form-control" name="news" id="news" placeholder="" required=""></textarea>
                        </div>
                        <div>
                               @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                               {{__('messages.save')}}
                                        </button>
                                     @else
                                           <button class="btn btn-primary florig" type="submit"> {{__('messages.submit')}}</button>
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
<script>
   CKEDITOR.replace('news');
</script>
@stop
