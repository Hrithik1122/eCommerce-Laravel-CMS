@extends('admin.index') @section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.EditTax')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.EditTax')}}</li>
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
      <strong class="card-title">{{__('messages.EditTax')}}</strong>
   </div>
   <div class="card-body">
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
               <form action="{{url('admin/updatetaxdata')}}" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="id" id="id" value="{{$taxes_data->id}}" /> {{csrf_field()}}
                  <div class="row form-group">
                     <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">{{__('messages.tax_name')}}<span class="reqfield">*</span></label>
                     </div>
                     <div class="col-12 col-md-9">
                        <input type="text" id="tax_class" placeholder="{{__('messages.tax_name')}}" class="form-control" name="tax_class" value="{{$taxes_data->tax_name}}" required>
                     </div>
                  </div>
                  <div class="row form-group">
                     <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">{{__('messages.based_on')}}<span class="reqfield">*</span></label>
                     </div>
                     <div class="col-12 col-md-9">
                        <select name="based_on" id="based_on" class="form-control">
                           <option value="1" <?=$taxes_data->base_on ==1 ? ' selected="selected"' : '';?>>{{__('messages.billing_address')}}</option>
                           <option value="2" <?=$taxes_data->base_on ==2 ? ' selected="selected"' : '';?>>{{__('messages.shipping_address')}}</option>
                        </select>
                     </div>
                  </div>
                  <div class="row form-group">
                     <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">{{__('messages.rate')}}(%)<span class="reqfield">*</span></label>
                     </div>
                     <div class="col-12 col-md-9">
                        <input type="text" id="rate" placeholder="{{__('messages.rate')}}" class="form-control" name="rate" value="{{$taxes_data->rate}}" required>
                     </div>
                  </div>
                  <div>
                       @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                               {{__('messages.update')}}
                                       </button>
                                  @else
                                         <button class="btn btn-primary florig" type="submit">{{__('messages.update')}}</button>
                                  @endif
                    
                  </div>
            </div>
         </div>
        </form>
      </div>
   </div>
</div>
@stop