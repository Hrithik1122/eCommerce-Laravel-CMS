@extends('admin.index') @section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.coupon')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="{{url('admin/coupon')}}">{{__('messages.coupon')}}</a></li>
               <li class="active">{{__('messages.add_coupon')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
<div class="rowset">
<div class="col-lg-9">
   <div class="card">
      <div class="card-header">
         <h4>{{__('messages.add_coupon')}}</h4>
      </div>
      <div class="card-body">
         <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
               <a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">  
               {{__('messages.general')}}
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
               {{__('messages.usage_res')}}
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="coupon-tab" data-toggle="tab" href="#coupon" role="tab" aria-controls="coupon" aria-selected="true">
               {{__('messages.usage_limit')}}
               </a>
            </li>
         </ul>
         <div class="tab-content pl-3 p-1" id="myTabContent">
            <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
               <div class="cmr1">
                  <input type="hidden" name="coupon_id" id="coupon_id" value="<?=isset($data->id)?$data->id:''; ?>" />
                  <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span>
                     </label>
                     <input id="name" name="name" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->name)?$data->name:''; ?>" placeholder="{{__('messages.name')}}">
                  </div>
                  <div class="form-group col-md-12 paddiv" >
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.discount_type')}}
                        </label>
                        <select id="discount_type" name="discount_type" class="form-control">
                           <option value="0" <?=isset($data->discount_type)&&$data->discount_type=='0'?'selected="selected"':''; ?> > {{__('messages.Fixed')}}
                           </option>
                           <option value="1" <?=isset($data->discount_type)&&$data->discount_type=='1'?'selected="selected"':''; ?>> {{__('messages.percentage')}}
                           </option>
                        </select>
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.value')}}
                        </label>
                        <input id="value" name="value" type="number" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->value)?$data->value:''; ?>" placeholder="{{__('messages.value')}}">
                     </div>
                  </div>
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.start_date')}}
                        </label>
                        <input id="start_date" name="start_date" type="text" class="form-control" value="<?=isset($data->start_date)?$data->start_date:''; ?>">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.end_date')}}
                        </label>
                        <input id="end_date" name="end_date" type="text" class="form-control" value="<?=isset($data->end_date)?$data->end_date:''; ?>">
                     </div>
                  </div>
                  <div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.code')}}
                        <span class="reqfield">*</span>
                        </label>
                        <input id="code" name="code" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->code)?$data->code:''; ?>" placeholder="{{__('messages.code')}}">
                     </div>
                     <div class="col-md-6 cmr1">
                        <div class="form-group col-md-12">
                           <div class="form-check">
                              <div class="status">
                                 <label for="status" class="form-check-label ">
                                 <input type="checkbox" id="status" name="status" value="1" class="form-check-input" <?=isset($data->status)&&$data->status=='1'?'checked="checked"':''; ?>>{{__('messages.enable_the_coupon')}}
                                 </label>
                              </div>
                           </div>
                        </div>
                        <div class="form-group col-md-12">
                           <div class="form-check">
                              <div class="status">
                                 <label for="free_shipping" class="form-check-label ">
                                 <input type="checkbox" id="free_shipping" name="free_shipping" value="1" class="form-check-input" <?=isset($data->free_shipping)&&$data->free_shipping=='1'?'checked="checked"':''; ?>>{{__('messages.allow_free_shipping')}}
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group col-md-12" >
                      @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                    {{__('messages.next')}}
                           </button>
                      @else
                            <button type="button" class="florig btn btn-primary" onclick="Savecoupon()">{{__('messages.next')}}</button>
                      @endif                   
                  </div>
               </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
               <div class="cmr1">
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.minmum_spend')}}</label>
                        <input id="minmum_send" name="minmum_send" type="text" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.minmum_spend')}}" value="<?=isset($data->minmum_spend)?$data->minmum_spend:''; ?>" onkeypress="return isNumberKey(event)">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.maximum_spend')}}
                        </label>
                        <input id="maximum_spend" name="maximum_spend" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->maximum_spend)?$data->maximum_spend:''; ?>" placeholder="{{__('messages.maximum_spend')}}" onchange="maxnumber(this.value)" onkeypress="return isNumberKey(event)">
                     </div>
                  </div>
                  <div class="form-group col-md-12">
                     <div class="form-check">
                        <div class="status">
                           <label for="coupon_on" class="form-check-label ">
                           <input type="checkbox" id="coupon_on" name="coupon_on" value="1" class="form-check-input" checked="" onchange="changeproductdiv()">Coupon On Product
                           </label>
                        </div>
                     </div>
                  </div>
                  <div class="form-group col-md-12" id="productcoupon">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.product')}}</label>
                     <input id="product" name="product" type="text" value="<?=isset($data->product)?$data->product:''; ?>">
                  </div>
                  <div class="form-group col-md-12 disno" id="categorycoupon">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.category')}}</label>
                     <input id="category" name="category" type="text" value="<?=isset($data->categories)?$data->categories:''; ?>">
                  </div>
                  <div class="form-group col-md-12" >
                      @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                    {{__('messages.next')}}
                           </button>
                      @else
                             <button type="button" class="btn btn-primary florig" onclick="SaveCouponstep2()">{{__('messages.next')}}</button>
                      @endif  
                    
                  </div>
               </div>
            </div>
            <div class="tab-pane fade" id="coupon" role="tabpanel" aria-labelledby="coupon-tab">
               <div class="cmr1">
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.usage_limit_per_coupon')}}</label>
                        <input id="per_coupon" name="per_coupon" type="number" class="form-control" aria-required="true" aria-invalid="false" placeholder="0" min='0' value="<?=isset($data->usage_limit_per_coupon)?$data->usage_limit_per_coupon:''; ?>">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.usage_limit_per_customer')}}</label>
                        <input id="per_customer" name="per_customer" type="number" class="form-control" aria-required="true" aria-invalid="false" placeholder="0" min='0' value="<?=isset($data->usage_limit_per_customer)?$data->usage_limit_per_customer:''; ?>">
                     </div>
                  </div>
                  <div class="form-group col-md-12" >
                      @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                    {{__('messages.finish')}}
                           </button>
                      @else
                             <button type="button" onclick="Savecouponstep3()" class="btn btn-primary florig">{{__('messages.finish')}}</button>
                      @endif  
                    
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<input type="hidden" id="coupon_code_use" value="{{__('messages_error_success.coupon_code_use')}}">
<input type="hidden" id="error_coupon_limit" value="{{__('messages_error_success.error_coupon_limit')}}">
<script type="text/javascript">
function maxnumber(val){
   var min=$("#minmum_send").val();
   if(parseInt(val)<parseInt(min)){
      alert($("#coupon_vaild_max").val());
      $("#maximum_spend").val("");
   }
}
</script>
@stop