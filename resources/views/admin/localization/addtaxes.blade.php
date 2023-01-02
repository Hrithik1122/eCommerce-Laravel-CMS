@extends('admin.index') @section('content')
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.add_tax')}}</h1>
         </div>
      </div>
   </div>
 <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.add_tax')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="rowset">
      <div class="col-lg-8">
         <div class="card">
            <div class="card-header">
               <strong class="card-title">{{__('messages.add_tax')}}</strong>
            </div>
            <div class="card-body">
               <form action="{{url('admin/storetaxes')}}" method="post" enctype="multipart/form-data">
                  {{csrf_field()}}
                  <div class="cmr1">
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
                           <div class="row form-group">
                              <div class="col col-md-3">
                                 <label for="text-input" class=" form-control-label">{{__('messages.tax_name')}}<span class="reqfield">*</span></label>
                              </div>
                              <div class="col-12 col-md-9">
                                 <input type="text" id="tax_class" placeholder="{{__('messages.tax_name')}}" class="form-control" name="tax_class" required>
                              </div>
                           </div>
                           <input type="hidden" name="based_on" value="1"/>
                        
                           <div class="row form-group">
                              <div class="col col-md-3">
                                 <label for="text-input" class=" form-control-label">{{__('messages.rate')}}(%)<span class="reqfield">*</span></label>
                              </div>
                              <div class="col-12 col-md-9">
                                 <input type="number" id="rate" step="any" placeholder="{{__('messages.rate')}}" class="form-control" name="rate" required>
                              </div>
                           </div>
                           <div>
                               @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                               {{__('messages.submit')}}
                                       </button>
                                  @else
                                         <button class="btn btn-primary florig" type="submit">{{__('messages.submit')}}</button>
                                  @endif
                             
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@stop