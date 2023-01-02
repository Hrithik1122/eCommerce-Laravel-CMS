@extends('admin.index')
@section('content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css">
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.report')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.report')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">

      <div class="col-md-12 col-lg-3">
         <div class="card">
            <div class="card-body">
               <div id="pay-invoice">
                  <div class="form-group has-success">
                     <label for="report" class="control-label mb-1">
                     {{__('messages.report')}} {{__('messages.type')}}
                     </label>
                     <select class="form-control" name="report" id="report" onchange="filterreport(this.value)" >
                        <option value="1" selected>{{__('messages.coupon_report')}}</option>
                        <option value="2">{{__('messages.customer_order_report')}}</option>
                        <option value="3">{{__('messages.pro_pur_report')}}</option>
                        <option value="4">{{__('messages.pro_stock_report')}}</option>
                        <option value="5">{{__('messages.sales_report')}}</option>
                        <option value="6">{{__('messages.shipping_report')}}</option>
                        <option value="7">{{__('messages.tax_report')}}</option>
                        <option value="8">{{__('messages.add_product_report')}}</option>
                        <option value="9">{{__('messages.top_seller_report')}}</option>
                        <option value="10">{{__('messages.add_customer_report')}}</option>
                        <option value="11">{{__('messages.add_coupon_report')}}</option>
                     </select>
                  </div>
                  <div id="filter_section">
                     <div class="form-group has-success report-date">
                        <label for="start_date" class="control-label mb-1">
                        {{__('messages.start_date')}}
                        </label>
                        <input type="text" class="form-control" name="start_date" id="start_date" >
                     </div>
                     <div class="form-group has-success report-date1">
                        <label for="start_date" class="control-label mb-1">
                        {{__('messages.end_date')}}
                        </label>
                        <input type="text" class="form-control" name="end_date" id="end_date">
                     </div>
                     <div class="form-group has-success">
                        <label for="status" class="control-label mb-1">
                        {{__('messages.order_status')}}
                        </label>
                        <select name="order_status" id="order_status" class="form-control">
                           <option value="">{{__("messages.select")}}</option>
                           <option value="6">{{__("messages.canceled")}}</option>
                           <option value="5">{{__("messages.completed")}}</option>
                           <option value="2">{{__("messages.on_hold")}}</option>
                           <option value="3">{{__("messages.pending")}}</option>
                           <option value="1">{{__("messages.processing")}}</option>
                           <option value="7">{{__("messages.refunded")}}</option>
                           <option value="4">{{__("messages.out_of_delivery")}}</option>
                        </select>
                     </div>
                     <div class="form-group has-success">
                        <label for="coupon_code" class="control-label mb-1">
                        {{__('messages.coupon')}} {{__('messages.code')}}
                        </label>
                        <input type="text" name="coupon_code" id="coupon_code" class="form-control">
                     </div>
                  </div>
                  <div class="form-group florig" >
                     <button class="btn btn-primary btn-flat m-b-30 m-t-30" onclick="filterdata()">{{__('messages.filter')}}</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-12 col-lg-9">
         <div class="card">
            <div class="card-body">
               <div id="pay-invoice">
                  <div id="result_section">
                     <div class="table-responsive dtdiv repo">
                        <table id="coupon_report" class="table table-striped table-bordered dttablewidth">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.coupon')}} {{__('messages.code')}}</th>
                                 <th>{{__('messages.coupon')}} {{__('messages.name')}}</th>
                                 <th>{{__('messages.orders')}}</th>
                                 <th>{{__('messages.total')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="customer_order_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.cus_name')}}</th>
                                 <th>{{__('messages.cus_email')}}</th>
                                 <th>{{__('messages.orders')}}</th>
                                 <th>{{__('messages.total')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="product_purchase_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.product')}}</th>
                                 <th>{{__('messages.qty')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="product_stock_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.product')}}</th>
                                 <th>{{__('messages.SKU')}}</th>
                                 <th>{{__('messages.stock_avilable')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="sales_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.orders')}}</th>
                                 <th>{{__('messages.product')}}</th>
                                 <th>{{__('messages.subtotal')}}</th>
                                 <th>{{__('messages.shipping')}}</th>
                                 <th>{{__('messages.taxes')}}</th>
                                 <th>{{__('messages.total')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="shipping_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.shipping_method')}}</th>
                                 <th>{{__('messages.orders')}}</th>
                                 <th>{{__('messages.total')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="tax_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.tax_name')}}</th>
                                 <th>{{__('messages.orders')}}</th>
                                 <th>{{__('messages.total')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="add_product_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.product')}}</th>
                                 <th>{{__('messages.SKU')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="top_seller_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.product')}}</th>
                                 <th>{{__('messages.SKU')}}</th>
                                 <th>{{__('messages.orders')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="add_customer_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.cus_name')}}</th>
                                 <th>{{__('messages.cus_email')}}</th>
                              </tr>
                           </thead>
                        </table>
                        <table id="add_coupon_report" class="table table-striped table-bordered dttablewidth disno">
                           <thead>
                              <tr>
                                 <th>{{__('messages.date')}}</th>
                                 <th>{{__('messages.coupon')}} {{__('messages.code')}}</th>
                                 <th>{{__('messages.coupon')}} {{__('messages.name')}}</th>
                                 <th>{{__('messages.coupon')}} {{__('messages.rate')}}</th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

</div>
<input type="hidden" id="report_not_select" value="{{__('messages_error_successs.report_not_select')}}">
<input type="hidden" id="start_date_txt" value='{{__("messages.start_date")}}'>
<input type="hidden" id="end_date_txt" value='{{__("messages.end_date")}}'>
<input type="hidden" id="order_status_txt" value='{{__("messages.order_status")}}'>
<input type="hidden" id="select_txt" value='{{__("messages.select")}}'> 
<input type="hidden" id="canceled_txt" value='{{__("messages.canceled")}}'>
<input type="hidden" id="completed_txt" value='{{__("messages.completed")}}'>
<input type="hidden" id="on_hold_txt" value='{{__("messages.on_hold")}}'>
<input type="hidden" id="pending_txt" value='{{__("messages.pending")}}'>
<input type="hidden" id="processing_txt" value='{{__("messages.processing")}}'>
<input type="hidden" id="refunded_txt" value='{{__("messages.refunded")}}'>
<input type="hidden" id="out_of_delivery_txt" value='{{__("messages.out_of_delivery")}}'>
<input type="hidden" id="coupon_code_txt" value='{{__("messages.coupon")}} {{__("messages.code")}}'>
<input type="hidden" id="cus_name_txt" value='{{__("messages.cus_name")}}'>
<input type="hidden" id="cus_email_txt" value='{{__("messages.cus_email")}}'>
<input type="hidden" id="product_txt" value='{{__("messages.product")}}'>
<input type="hidden" id="SKU_txt" value='{{__("messages.SKU")}}'>
<input type="hidden" id="product_name_txt" value='{{__("messages.product_name")}}'>
<input type="hidden" id="stock_avilable_txt" value='{{__("messages.stock_avilable")}}'>
<input type="hidden" id="in_stock_txt" value='{{__("messages.in_stock")}}'>
<input type="hidden" id="outstock_txt" value='{{__("messages.outstock")}}'>
<input type="hidden" id="shipping_method_txt" value='{{__("messages.shipping_method")}}'>
<input type="hidden" id="home_delivery_txt" value='{{__("messages.home_delivery")}}'>
<input type="hidden" id="local_pickup_txt" value='{{__("messages.local_pickup")}}'>
<input type="hidden" id="tax_name" value='{{__("messages.tax_name")}}'>
@stop